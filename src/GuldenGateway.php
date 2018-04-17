<?php

namespace Pronamic\WordPress\Pay\Extensions\Charitable;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Title: Charitable Gulden gateway
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Reüel van der Steege
 * @version unreleased
 * @since   unreleased
 */
class GuldenGateway extends Gateway {
	/**
	 * The unique ID of this payment gateway
	 *
	 * @var string
	 */
	const ID = 'pronamic_pay_gulden';

	/**
	 * Constructs and initialize an Gulden gateway
	 */
	public function __construct() {
		parent::__construct();

		$this->name = PaymentMethods::get_name( PaymentMethods::GULDEN );

		$this->defaults = array(
			'label' => PaymentMethods::get_name( PaymentMethods::GULDEN ),
		);

		$this->payment_method = PaymentMethods::GULDEN;
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
