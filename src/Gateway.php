<?php
/**
 * Gateway.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay
 */

namespace Pronamic\WordPress\Pay\Extensions\Charitable;

use Charitable_Donation_Processor;
use Charitable_Gateway;
use Pronamic\WordPress\Money\Currency;
use Pronamic\WordPress\Money\Money;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Plugin;
use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Title: Charitable gateway
 * Description:
 * Copyright: 2005-2024 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.1.2
 * @since   1.0.0
 */
class Gateway extends Charitable_Gateway {
	/**
	 * The unique ID of this payment gateway
	 *
	 * @var string
	 */
	const ID = 'pronamic_pay';

	/**
	 * The payment method
	 *
	 * @var string|null
	 */
	protected $payment_method;

	/**
	 * Constructs and initialize an iDEAL gateway
	 */
	public function __construct() {
		// Name.
		$this->name = __( 'Knit Pay', 'pronamic_ideal' );

		if ( null !== $this->payment_method ) {
			$this->name = \sprintf(
				'%s - %s',
				$this->name,
				PaymentMethods::get_name( $this->payment_method )
			);
		}

		$this->defaults = [
			'label' => PaymentMethods::get_name( $this->payment_method, \__( 'Knit Pay', 'pronamic_ideal' ) ),
		];

		// @link https://github.com/Charitable/Charitable/blob/1.4.5/includes/gateways/class-charitable-gateway-paypal.php#L41-L44
		$this->supports = [
			'1.3.0',
		];
	}

	/**
	 * Register gateway settings.
	 *
	 * @param   array<string, mixed> $settings Settings.
	 *
	 * @return  array<string, mixed>
	 * @since   1.0.0
	 */
	public function gateway_settings( $settings ) {
		$settings['config_id'] = [
			'type'     => 'select',
			'title'    => __( 'Configuration', 'pronamic_ideal' ),
			'priority' => 8,
			'options'  => Plugin::get_config_select_options( $this->payment_method ),
			'default'  => get_option( 'pronamic_pay_config_id' ),
		];

		$settings['transaction_description'] = [
			'type'     => 'text',
			'title'    => __( 'Transaction description', 'pronamic_ideal' ),
			'priority' => 8,
			'default'  => __( 'Charitable donation {donation_id}', 'pronamic_ideal' ),
			/* translators: %s: <code>{tag}</code> */
			'help'     => sprintf( __( 'Available tags: %s', 'pronamic_ideal' ), sprintf( '<code>%s</code> <code>%s</code> <code>%s</code>', '{donation_id}', '{first_campaign_name}', '{campaign_name}' ) ),
		];

		if ( null === $this->payment_method ) {
			$settings['gateway_info'] = [
				'type'     => 'content',
				'title'    => '',
				'priority' => 8,
				'content'  => sprintf( '<p><em>%s</em></p>', __( "This payment method does not use a predefined payment method for the payment. Some payment providers list all activated payment methods for your account to choose from. Use payment method specific gateways (such as 'Razorpay') to let customers choose their desired payment method at checkout.", 'pronamic_ideal' ) ),
			];
		}

		return $settings;
	}

	/**
	 * Get Pronamic payment method.
	 *
	 * @return string|null
	 */
	public function get_pronamic_payment_method() {
		return $this->payment_method;
	}

	/**
	 * Get Pronamic gateway configuration ID.
	 *
	 * @return int|null
	 */
	public function get_pronamic_config_id() {
		$config_id = intval( $this->get_value( 'config_id' ) );

		// Use default gateway if no configuration has been set.
		if ( empty( $config_id ) ) {
			$config_id = intval( \get_option( 'pronamic_pay_config_id' ) );
		}

		return empty( $config_id ) ? null : $config_id;
	}

	/**
	 * Process donation.
	 *
	 * @param mixed                         $return      Return.
	 * @param int                           $donation_id Donation ID.
	 * @param Charitable_Donation_Processor $processor   Charitable donation processor.
	 * @return bool|array<string,mixed>
	 */
	public static function process_donation( $return, $donation_id, $processor ) {
		return self::pronamic_process_donation( $return, $donation_id, $processor, new static() );
	}

	/**
	 * Process donation.
	 *
	 * @param mixed                         $return             Return.
	 * @param int                           $donation_id        Donation ID.
	 * @param Charitable_Donation_Processor $processor          Charitable donation processor.
	 * @param Gateway                       $charitable_gateway Charitable gateway.
	 *
	 * @return bool|array<string,mixed>
	 */
	public static function pronamic_process_donation( $return, $donation_id, Charitable_Donation_Processor $processor, Gateway $charitable_gateway ) {
		$payment_method = $charitable_gateway->payment_method;

		$config_id = $charitable_gateway->get_pronamic_config_id();

		$gateway = Plugin::get_gateway( (int) $config_id );

		if ( ! $gateway ) {
			return false;
		}

		// Data.
		$user_data = $processor->get_donation_data_value( 'user' );

		/**
		 * Build payment.
		 */
		$payment = new Payment();

		$payment->source    = 'charitable';
		$payment->source_id = \strval( $donation_id );
		$payment->order_id  = \strval( $donation_id );

		// Description.
		$payment->set_description( CharitableHelper::get_description( $charitable_gateway, $donation_id ) );

		$payment->title = CharitableHelper::get_title( $donation_id );

		// Customer.
		$payment->set_customer( CharitableHelper::get_customer_from_user_data( $user_data ) );

		// Address.
		$payment->set_billing_address( CharitableHelper::get_address_from_user_data( $user_data ) );

		// Currency.
		$currency = Currency::get_instance( \charitable_get_currency() );

		// Amount.
		$payment->set_total_amount( new Money( CharitableHelper::get_total_amount_value( $donation_id ), $currency ) );

		// Method.
		$payment->set_payment_method( $payment_method );

		// Configuration.
		$payment->config_id = $config_id;

		try {
			$payment = Plugin::start_payment( $payment );
		} catch ( \Exception $e ) {
			charitable_get_notices()->add_error( Plugin::get_default_error_message() );
			charitable_get_notices()->add_error( $e->getMessage() );

			return false;
		}

		return [
			'redirect' => $payment->get_pay_redirect_url(),
			'safe'     => false,
		];
	}

	/**
	 * Returns the current gateway's ID.
	 *
	 * @since  1.0.3
	 *
	 * @return string
	 */
	public static function get_gateway_id() {
		return static::ID;
	}
}
