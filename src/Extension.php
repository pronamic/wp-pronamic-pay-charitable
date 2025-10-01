<?php
/**
 * Extension.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay
 */

namespace Pronamic\WordPress\Pay\Extensions\Charitable;

use Charitable_Donation;
use Charitable_Gateway;
use Pronamic\WordPress\Pay\AbstractPluginIntegration;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Payments\PaymentStatus;
use Pronamic\WordPress\Pay\Core\Util as Core_Util;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Plugin;

/**
 * Title: Charitable extension
 * Description:
 * Copyright: 2005-2024 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.3
 * @since   1.0.0
 */
class Extension extends AbstractPluginIntegration {
	/**
	 * Slug
	 *
	 * @var string
	 */
	const SLUG = 'charitable';

	/**
	 * Construct Charitable plugin integration.
	 */
	public function __construct() {
		parent::__construct(
			[
				'name' => 'Charitable',
			]
		);

		// Dependencies.
		$dependencies = $this->get_dependencies();

		$dependencies->add( new CharitableDependency() );
	}

	/**
	 * Setup plugin integration.
	 *
	 * @return void
	 */
	public function setup() {
		add_filter( 'pronamic_payment_source_text_' . self::SLUG, [ $this, 'source_text' ], 10, 2 );
		add_filter( 'pronamic_payment_source_description_' . self::SLUG, [ $this, 'source_description' ], 10, 2 );
		add_filter( 'pronamic_payment_source_url_' . self::SLUG, [ $this, 'source_url' ], 10, 2 );

		// Check if dependencies are met and integration is active.
		if ( ! $this->is_active() ) {
			return;
		}

		add_filter( 'pronamic_payment_redirect_url_' . self::SLUG, [ $this, 'redirect_url' ], 10, 2 );
		add_action( 'pronamic_payment_status_update_' . self::SLUG, [ $this, 'status_update' ], 10 );

		add_filter( 'charitable_payment_gateways', [ $this, 'charitable_payment_gateways' ] );

		// @link https://github.com/Charitable/Charitable/blob/1.4.5/includes/donations/class-charitable-donation-form.php#L387
		\add_filter( 'charitable_donation_form_gateway_fields', [ $this, 'form_gateway_fields' ], 10, 2 );

		// @link https://github.com/Charitable/Charitable/blob/1.4.5/includes/abstracts/class-charitable-form.php#L231-L232
		\add_filter( 'charitable_form_field_template', [ $this, 'form_field_template' ], 10, 4 );
	}

	/**
	 * Form gateway fields.
	 *
	 * @see   https://github.com/Charitable/Charitable/blob/1.4.5/includes/donations/class-charitable-donation-form.php#L387
	 * @since 1.0.2
	 *
	 * @param array<int|string, mixed> $fields             Fields.
	 * @param Charitable_Gateway      $charitable_gateway Gateway.
	 *
	 * @return array<int|string, mixed>
	 */
	public static function form_gateway_fields( $fields, $charitable_gateway ) {
		if ( ! $charitable_gateway instanceof Gateway ) {
			return $fields;
		}

		$config_id = $charitable_gateway->get_pronamic_config_id();

		$gateway = Plugin::get_gateway( (int) $config_id );

		if ( null === $gateway ) {
			return $fields;
		}

		$payment_method = $gateway->get_payment_method( (string) $charitable_gateway->get_pronamic_payment_method() );

		if ( null === $payment_method ) {
			return $fields;
		}

		$pronamic_fields = $payment_method->get_fields();

		foreach ( $pronamic_fields as $field ) {
			$fields[] = [
				'type'               => 'pronamic_pay_field',
				'pronamic_pay_field' => $field->render(),
			];
		}

		return $fields;
	}

	/**
	 * Form gateway field template.
	 *
	 * @see   https://github.com/Charitable/Charitable/blob/1.4.5/includes/abstracts/class-charitable-form.php#L231-L232
	 * @since 1.0.2
	 *
	 * @param false|Charitable_Template $template False by default.
	 * @param array                     $field    Field definition.
	 * @param Charitable_Form           $form     The Charitable_Form object.
	 * @param int                       $index    The current index.
	 *
	 * @return string|false
	 */
	public static function form_field_template( $template, $field, $form, $index ) {
		if ( ! \array_key_exists( 'type', $field ) ) {
			return $template;
		}

		if ( 'pronamic_pay_field' !== $field['type'] ) {
			return $template;
		}

		if ( ! \array_key_exists( 'pronamic_pay_field', $field ) ) {
			return $template;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $field['pronamic_pay_field'];

		return false;
	}

	/**
	 * Charitable payments gateways.
	 *
	 * @link https://github.com/Charitable/Charitable/blob/1.1.4/includes/gateways/class-charitable-gateways.php#L44-L51
	 * @param array<string, string> $gateways Gateways.
	 * @return array<string, string>
	 */
	public function charitable_payment_gateways( $gateways ) {
		$classes = [
			Gateway::class,
			BankTransferGateway::class,
			CreditCardGateway::class,
			DirectDebitGateway::class,
			IDealGateway::class,
			BancontactGateway::class,
			SofortGateway::class,
		];

		if ( PaymentMethods::is_active( PaymentMethods::PAYPAL ) ) {
			$classes[] = PayPalGateway::class;
		}

		foreach ( $classes as $class ) {
			$id = $class::get_gateway_id();

			$gateways[ $id ] = $class;

			// @link https://github.com/Charitable/Charitable/blob/1.1.4/includes/donations/class-charitable-donation-processor.php#L165-L174
			// @link https://github.com/Charitable/Charitable/blob/1.4.5/includes/donations/class-charitable-donation-processor.php#L213-L247
			\add_filter( 'charitable_process_donation_' . $id, [ $class, 'process_donation' ], 10, 3 );
		}

		return $gateways;
	}

	/**
	 * Get the default return URL.
	 *
	 * @since 1.0.3
	 * @param Charitable_Donation $donation Donation.
	 * @return string URL
	 */
	private static function get_return_url( Charitable_Donation $donation ) {
		$url = home_url();

		$donations = $donation->get_campaign_donations();

		$campaign = reset( $donations );

		if ( false !== $campaign && \property_exists( $campaign, 'campaign_id' ) ) {
			$permalink = get_permalink( $campaign->campaign_id );

			if ( false !== $permalink ) {
				$url = $permalink;
			}
		}

		return $url;
	}

	/**
	 * Payment redirect URL filter.
	 *
	 * @param string  $url     Redirect URL.
	 * @param Payment $payment Payment.
	 *
	 * @return string
	 */
	public function redirect_url( $url, Payment $payment ) {
		$donation_id = $payment->get_source_id();

		$donation = new Charitable_Donation( $donation_id );

		$url = self::get_return_url( $donation );

		switch ( $payment->get_status() ) {
			case PaymentStatus::SUCCESS:
				$permalink = charitable_get_permalink(
					'donation_receipt_page',
					[
						'donation_id' => $donation_id,
					]
				);

				if ( false !== $permalink ) {
					$url = $permalink;
				}

				break;
		}

		return $url;
	}

	/**
	 * Update lead status of the specified payment
	 *
	 * @link https://github.com/Charitable/Charitable/blob/1.1.4/includes/gateways/class-charitable-gateway-paypal.php#L229-L357
	 *
	 * @param Payment $payment Payment.
	 * @return void
	 */
	public function status_update( Payment $payment ) {
		$donation_id = $payment->get_source_id();

		$donation = new Charitable_Donation( $donation_id );

		/* Save the transaction ID */
		$transaction_id = $payment->get_transaction_id();

		if ( ! empty( $transaction_id ) ) {
			$donation->set_gateway_transaction_id( $transaction_id );
		}

		switch ( $payment->get_status() ) {
			case PaymentStatus::CANCELLED:
				$donation->update_status( 'charitable-cancelled' );

				break;
			case PaymentStatus::EXPIRED:
			case PaymentStatus::FAILURE:
				$donation->update_status( 'charitable-failed' );

				break;
			case PaymentStatus::SUCCESS:
				$donation->update_status( 'charitable-completed' );

				break;
			case PaymentStatus::OPEN:
			default:
				$donation->update_status( 'charitable-pending' );

				break;
		}
	}

	/**
	 * Source column
	 *
	 * @param string  $text    Source text.
	 * @param Payment $payment Payment.
	 *
	 * @return string
	 */
	public function source_text( $text, Payment $payment ) {
		$text = __( 'Charitable', 'pronamic_ideal' ) . '<br />';

		$text .= sprintf(
			'<a href="%s">%s</a>',
			get_edit_post_link( (int) $payment->source_id ),
			/* translators: %s: source id */
			sprintf( __( 'Donation %s', 'pronamic_ideal' ), $payment->source_id )
		);

		return $text;
	}

	/**
	 * Source description.
	 *
	 * @param string  $description Source description.
	 * @param Payment $payment     Payment.
	 *
	 * @return string
	 */
	public function source_description( $description, Payment $payment ) {
		return __( 'Charitable Donation', 'pronamic_ideal' );
	}

	/**
	 * Source URL.
	 *
	 * @param string  $url     Source URL.
	 * @param Payment $payment Payment.
	 *
	 * @return null|string
	 */
	public function source_url( $url, Payment $payment ) {
		return get_edit_post_link( (int) $payment->source_id );
	}
}
