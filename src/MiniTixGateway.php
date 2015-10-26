<?php

/**
 * Title: Charitable MiniTix gateway
 * Description:
 * Copyright: Copyright (c) 2005 - 2015
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Extensions_Charitable_MiniTixGateway extends Pronamic_WP_Pay_Extensions_Charitable_Gateway {
	/**
	 * The unique ID of this payment gateway
	 *
	 * @var string
	 */
	const ID = 'pronamic_pay_minitix';

	//////////////////////////////////////////////////

	/**
	 * Constructs and initialize an iDEAL gateway
	 */
	public function __construct() {
		parent::__construct();

		$this->name = __( 'MiniTix', 'pronamic_ideal' );

		$this->defaults = array(
			'label' => __( 'MiniTix', 'pronamic_ideal' )
		);

		$this->payment_method = Pronamic_WP_Pay_PaymentMethods::MINITIX;
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
