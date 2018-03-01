<?php

namespace Pronamic\WordPress\Pay\Extensions\Charitable;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Title: Charitable Bank Transfer gateway
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 1.1.0
 * @since   1.0.0
 */
class BankTransferGateway extends Gateway {
	/**
	 * The unique ID of this payment gateway
	 *
	 * @var string
	 */
	const ID = 'pronamic_pay_bank_transfer';

	/**
	 * Constructs and initialize an iDEAL gateway
	 */
	public function __construct() {
		parent::__construct();

		$this->name = __( 'Bank Transfer', 'pronamic_ideal' );

		$this->defaults = array(
			'label' => __( 'Bank Transfer', 'pronamic_ideal' ),
		);

		$this->payment_method = PaymentMethods::BANK_TRANSFER;
	}

	/**
	 * Process donation.
	 *
	 * @since   1.0.2
	 *
	 * @param $return
	 * @param $donation_id
	 * @param $processor
	 *
	 * @return mixed
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
