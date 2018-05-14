<?php

namespace Pronamic\WordPress\Pay\Extensions\Charitable;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Title: Charitable Sofort gateway
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class SofortGateway extends Gateway {
	/**
	 * The unique ID of this payment gateway
	 *
	 * @var string
	 */
	const ID = 'pronamic_pay_sofort';

	/**
	 * Constructs and initialize an iDEAL gateway
	 */
	public function __construct() {
		parent::__construct();

		$this->name = __( 'SOFORT Banking', 'pronamic_ideal' );

		$this->defaults = array(
			'label' => __( 'SOFORT Banking', 'pronamic_ideal' ),
		);

		$this->payment_method = PaymentMethods::SOFORT;
	}

	/**
	 * Process donation.
	 *
	 * @since   1.0.2
	 */
	public static function process_donation( $return, $donation_id, $processor ) {
		return self::pronamic_process_donation( $return, $donation_id, $processor, new self() );
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
