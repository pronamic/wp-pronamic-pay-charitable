<?php

/**
 * Title: Charitable gateway
 * Description:
 * Copyright: Copyright (c) 2005 - 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.1.1
 * @since 1.0.0
 */
class Pronamic_WP_Pay_Extensions_Charitable_Gateway extends Charitable_Gateway {
	/**
	 * The unique ID of this payment gateway
	 *
	 * @var string
	 */
	const ID = 'pronamic_pay';

	/**
	 * The payment method
	 *
	 * @var string
	 */
	protected $payment_method;

	//////////////////////////////////////////////////

	/**
	 * Constructs and initialize an iDEAL gateway
	 */
	public function __construct() {
		$this->name = __( 'Pronamic', 'pronamic_ideal' );

		$this->defaults = array(
			'label' => __( 'Pronamic', 'pronamic_ideal' ),
		);

		// @see https://github.com/Charitable/Charitable/blob/1.4.5/includes/gateways/class-charitable-gateway-paypal.php#L41-L44
		$this->supports = array(
			'1.3.0',
		);
	}

	/**
	 * Register gateway settings.
	 *
	 * @param   array   $settings
	 * @return  array
	 * @since   1.0.0
	 */
	public function gateway_settings( $settings ) {
		$settings['config_id'] = array(
			'type'     => 'select',
			'title'    => __( 'Configuration', 'pronamic_ideal' ),
			'priority' => 8,
			'options'  => Pronamic_WP_Pay_Plugin::get_config_select_options( $this->payment_method ),
			'default'  => get_option( 'pronamic_pay_config_id' ),
		);

		$settings['transaction_description'] = array(
			'type'     => 'text',
			'title'    => __( 'Transaction description', 'pronamic_ideal' ),
			'priority' => 8,
			'default'  => __( 'Charitable donation {donation_id}', 'pronamic_ideal' ),
			'help'     => sprintf( __( 'Available tags: %s', 'pronamic_ideal' ), sprintf( '<code>%s</code>', '{donation_id}' ) ),
		);

		if ( null === $this->payment_method ) {
			$settings['gateway_info'] = array(
				'type'     => 'content',
				'title'    => '',
				'priority' => 8,
				'content'  => sprintf( '<p><em>%s</em></p>', __( "This payment method does not use a predefined payment method for the payment. Some payment providers list all activated payment methods for your account to choose from. Use payment method specific gateways (such as 'iDEAL') to let customers choose their desired payment method at checkout.", 'pronamic_ideal' ) ),
			);
		}

		return $settings;
	}

	/**
	 * Process donation.
	 *
	 * @since   1.1.1
	 */
	public static function process_donation( $return, $donation_id, $processor ) {
		return self::pronamic_process_donation( $return, $donation_id, $processor, new self() );
	}

	/**
	 * Process donation.
	 *
	 * @since   1.0.0
	 * @param   mixed                          $return
	 * @param   int                            $donation_id
	 * @param   Charitable_Donation_Processor  $processor
	 * @param   string                         $gateway
	 * @return mixed array or boolean
	 */
	public static function pronamic_process_donation( $return, $donation_id, $processor, $charitable_gateway ) {
		$payment_method = $charitable_gateway->payment_method;

		$config_id = $charitable_gateway->get_value( 'config_id' );

		$gateway = Pronamic_WP_Pay_Plugin::get_gateway( $config_id );

		if ( ! $gateway ) {
			return false;
		}

		// Data
		$data = new Pronamic_WP_Pay_Extensions_Charitable_PaymentData( $donation_id, $processor, $charitable_gateway );

		$gateway->set_payment_method( $payment_method );

		$payment = Pronamic_WP_Pay_Plugin::start( $config_id, $gateway, $data, $payment_method );

		$error = $gateway->get_error();

		if ( is_wp_error( $error ) ) {
			charitable_get_notices()->add_error( $error->get_error_message() );

			return false;
		}

		return array(
			'redirect' => $payment->get_pay_redirect_url(),
			'safe'     => false,
		);
	}

	/**
	 * Returns the current gateway's ID.
	 *
	 * @return  string
	 * @access  public
	 * @static
	 * @since   1.0.3
	 */
	public static function get_gateway_id() {
		return self::ID;
	}
}
