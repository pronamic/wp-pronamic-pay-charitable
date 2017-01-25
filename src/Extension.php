<?php

/**
 * Title: Charitable extension
 * Description:
 * Copyright: Copyright (c) 2005 - 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.1.1
 * @since 1.0.0
 */
class Pronamic_WP_Pay_Extensions_Charitable_Extension {
	/**
	 * Slug
	 *
	 * @var string
	 */
	const SLUG = 'charitable';

	//////////////////////////////////////////////////

	/**
	 * Bootstrap
	 */
	public static function bootstrap() {
		new self();
	}

	/**
	 * Construct and initializes an Charitable extension object.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );

		add_filter( 'charitable_payment_gateways', array( $this, 'charitable_payment_gateways' ) );

		add_filter( 'pronamic_payment_redirect_url_' . self::SLUG, array( __CLASS__, 'redirect_url' ), 10, 2 );
		add_action( 'pronamic_payment_status_update_' . self::SLUG, array( __CLASS__, 'status_update' ), 10 );
		add_filter( 'pronamic_payment_source_text_' . self::SLUG,   array( __CLASS__, 'source_text' ), 10, 2 );
		add_filter( 'pronamic_payment_source_description_' . self::SLUG,   array( __CLASS__, 'source_description' ), 10, 2 );
		add_filter( 'pronamic_payment_source_url_' . self::SLUG,   array( __CLASS__, 'source_url' ), 10, 2 );
	}

	//////////////////////////////////////////////////

	/**
	 * Initialize
	 */
	public function init() {

	}

	/**
	 * Charitable payments gateways.
	 *
	 * @see https://github.com/Charitable/Charitable/blob/1.1.4/includes/gateways/class-charitable-gateways.php#L44-L51
	 * @param array $gateways
	 * @retrun array
	 */
	public function charitable_payment_gateways( $gateways ) {
		$classes = array(
			'Pronamic_WP_Pay_Extensions_Charitable_Gateway',
			'Pronamic_WP_Pay_Extensions_Charitable_BankTransferGateway',
			'Pronamic_WP_Pay_Extensions_Charitable_CreditCardGateway',
			'Pronamic_WP_Pay_Extensions_Charitable_DirectDebitGateway',
			'Pronamic_WP_Pay_Extensions_Charitable_IDealGateway',
			'Pronamic_WP_Pay_Extensions_Charitable_MisterCashGateway',
			'Pronamic_WP_Pay_Extensions_Charitable_SofortGateway',
		);

		foreach ( $classes as $class ) {
			$id = call_user_func( array( $class, 'get_gateway_id' ) );

			$gateways[ $id ] = $class;

			// @see https://github.com/Charitable/Charitable/blob/1.1.4/includes/donations/class-charitable-donation-processor.php#L165-L174
			// @see https://github.com/Charitable/Charitable/blob/1.4.5/includes/donations/class-charitable-donation-processor.php#L213-L247
			add_filter( 'charitable_process_donation_' . $id, array( $class, 'process_donation' ), 10, 3 );

			if ( Pronamic_WP_Pay_Class::method_exists( $class, 'form_gateway_fields' ) ) {
				// @see https://github.com/Charitable/Charitable/blob/1.4.5/includes/donations/class-charitable-donation-form.php#L387
				add_filter( 'charitable_donation_form_gateway_fields', array( $class, 'form_gateway_fields' ), 10, 2 );
			}

			if ( Pronamic_WP_Pay_Class::method_exists( $class, 'form_field_template' ) ) {
				// @see https://github.com/Charitable/Charitable/blob/1.4.5/includes/abstracts/class-charitable-form.php#L231-L232
				add_filter( 'charitable_form_field_template', array( $class, 'form_field_template' ), 10, 4 );
			}
		}

		return $gateways;
	}

	//////////////////////////////////////////////////

	/**
	 * Get the default return URL.
	 *
	 * @since 1.0.3
	 * @param Charitable_Donation $donation
	 * @return string URL
	 */
	private static function get_return_url( $donation ) {
		$url = home_url();

		$campaign = reset( $donation->get_campaign_donations() );

		if ( false !== $campaign ) {
			$url = get_permalink( $campaign->campaign_id );
		}

		return $url;
	}

	/**
	 * Payment redirect URL filter.
	 *
	 * @param string                  $url
	 * @param Pronamic_WP_Pay_Payment $payment
	 * @return string
	 */
	public static function redirect_url( $url, $payment ) {
		$donation_id = $payment->get_source_id();

		$donation = new Charitable_Donation( $donation_id );

		$url = self::get_return_url( $donation );

		switch ( $payment->get_status() ) {
			case Pronamic_WP_Pay_Statuses::SUCCESS :
				$url = charitable_get_permalink( 'donation_receipt_page', array( 'donation_id' => $donation_id ) );

				break;
		}

		return $url;
	}

	//////////////////////////////////////////////////

	/**
	 * Update lead status of the specified payment
	 *
	 * @see https://github.com/Charitable/Charitable/blob/1.1.4/includes/gateways/class-charitable-gateway-paypal.php#L229-L357
	 * @param Pronamic_Pay_Payment $payment
	 */
	public static function status_update( Pronamic_Pay_Payment $payment ) {
		$donation_id = $payment->get_source_id();

		$donation = new Charitable_Donation( $donation_id );

		switch ( $payment->get_status() ) {
			case Pronamic_WP_Pay_Statuses::CANCELLED :
				$donation->update_status( 'charitable-cancelled' );

				break;
			case Pronamic_WP_Pay_Statuses::EXPIRED :
				$donation->update_status( 'charitable-failed' );

				break;
			case Pronamic_WP_Pay_Statuses::FAILURE :
				$donation->update_status( 'charitable-failed' );

				break;
			case Pronamic_WP_Pay_Statuses::SUCCESS :
				$donation->update_status( 'charitable-completed' );

				break;
			case Pronamic_WP_Pay_Statuses::OPEN :
			default:
				$donation->update_status( 'charitable-pending' );

				break;
		}
	}

	//////////////////////////////////////////////////

	/**
	 * Source column
	 */
	public static function source_text( $text, Pronamic_WP_Pay_Payment $payment ) {
		$text  = '';

		$text .= __( 'Charitable', 'pronamic_ideal' ) . '<br />';

		$text .= sprintf(
			'<a href="%s">%s</a>',
			get_edit_post_link( $payment->source_id ),
			sprintf( __( 'Donation %s', 'pronamic_ideal' ), $payment->source_id )
		);

		return $text;
	}

	/**
	 * Source description.
	 */
	public static function source_description( $description, Pronamic_Pay_Payment $payment ) {
		$description = __( 'Charitable Donation', 'pronamic_ideal' );

		return $description;
	}

	/**
	 * Source URL.
	 */
	public static function source_url( $url, Pronamic_Pay_Payment $payment ) {
		$url = get_edit_post_link( $payment->source_id );

		return $url;
	}
}
