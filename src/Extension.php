<?php

/**
 * Title: Charitable extension
 * Description:
 * Copyright: Copyright (c) 2005 - 2015
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.1.0
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

		add_action( 'pronamic_payment_status_update_' . self::SLUG, array( __CLASS__, 'status_update' ), 10, 2 );
		add_filter( 'pronamic_payment_source_text_' . self::SLUG,   array( __CLASS__, 'source_text' ), 10, 2 );

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
			'Pronamic_WP_Pay_Extensions_Charitable_MiniTixGateway',
			'Pronamic_WP_Pay_Extensions_Charitable_MisterCashGateway',
			'Pronamic_WP_Pay_Extensions_Charitable_SofortGateway',
		);

		foreach ( $pronamic_gateways as $id => $class ) {
			$id = call_user_func( array( $class, 'get_gateway_id' ) );

			$gateways[ $id ] = $class;

			// @see https://github.com/Charitable/Charitable/blob/1.1.4/includes/donations/class-charitable-donation-processor.php#L165-L174
			add_action( 'charitable_process_donation_' . $id, array( $class, 'process_donation' ), 10, 2 );
		}

		return $gateways;
	}

	//////////////////////////////////////////////////

	/**
	 * Update lead status of the specified payment
	 *
	 * @see https://github.com/Charitable/Charitable/blob/1.1.4/includes/gateways/class-charitable-gateway-paypal.php#L229-L357
	 * @param Pronamic_Pay_Payment $payment
	 */
	public static function status_update( Pronamic_Pay_Payment $payment, $can_redirect = false ) {
		$donation_id = $payment->get_source_id();

		$donation = new Charitable_Donation( $donation_id );

		switch ( $payment->get_status() ) {
			case Pronamic_WP_Pay_Statuses::CANCELLED :
				$donation->update_status( 'charitable-pending' );

				$url = home_url();

				break;
			case Pronamic_WP_Pay_Statuses::EXPIRED :
				$donation->update_status( 'charitable-failed' );

				$url = home_url();

				break;
			case Pronamic_WP_Pay_Statuses::FAILURE :
				$donation->update_status( 'charitable-failed' );

				$url = home_url();

				break;
			case Pronamic_WP_Pay_Statuses::SUCCESS :
				$donation->update_status( 'charitable-completed' );

				$url = charitable_get_permalink( 'donation_receipt_page', array( 'donation_id' => $donation_id ) );

				break;
			case Pronamic_WP_Pay_Statuses::OPEN :
			default:
				$donation->update_status( 'charitable-pending' );

				$url = home_url();

				break;
		}

		if ( $can_redirect ) {
			wp_redirect( $url );

			exit;
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
}
