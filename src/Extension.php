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
		$pronamic_gateways = array(
			'pronamic_pay'       => 'Pronamic_WP_Pay_Extensions_Charitable_Gateway',
			'pronamic_pay_ideal' => 'Pronamic_WP_Pay_Extensions_Charitable_IDealGateway',
		);

		foreach ( $pronamic_gateways as $id => $class ) {
			$gateways[ $id ] = $class;

			// @see https://github.com/Charitable/Charitable/blob/1.1.4/includes/donations/class-charitable-donation-processor.php#L165-L174
			add_action( 'charitable_process_donation_' . $id, array( $class, 'process_donation' ), 10, 2 );
		}

		return $gateways;
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
