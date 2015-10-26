<?php

/**
 * Title: Charitable gateway
 * Description:
 * Copyright: Copyright (c) 2005 - 2015
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Extensions_Charitable_Gateway extends Charitable_Gateway {
	/**
	 * The unique ID of this payment gateway
	 *
	 * @var string
	 */
	const ID = 'pronamic';

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
			'label' => __( 'Pronamic', 'pronamic_ideal' )
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
            'type'      => 'select',
            'title'     => __( 'Configuration', 'pronamic_ideal' ), 
            'priority'  => 8,
            'options'   => Pronamic_WP_Pay_Plugin::get_config_select_options( $this->payment_method ),
        );

        return $settings;
    }

    public static function process_donation( $donation_id, $processor ) {
        var_dump( $donation_id );
        exit;
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
