<?php
/**
 * PayPal gateway.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay
 */

namespace Pronamic\WordPress\Pay\Extensions\Charitable;

use Charitable_Donation_Processor;
use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Title: Charitable PayPal gateway
 * Description:
 * Copyright: 2005-2022 Pronamic
 * Company: Pronamic
 *
 * @author  Reüel van der Steege
 * @version 2.3.0
 * @since   2.3.0
 */
class PayPalGateway extends Gateway {
	/**
	 * The unique ID of this payment gateway
	 *
	 * @var string
	 */
	const ID = 'pronamic_pay_paypal';

	/**
	 * Payment method.
	 *
	 * @var string
	 */
	protected $payment_method = PaymentMethods::PAYPAL;

	/**
	 * Process donation.
	 *
	 * @since   2.3.0
	 *
	 * @param bool|array                    $return      Return.
	 * @param int                           $donation_id Donation ID.
	 * @param Charitable_Donation_Processor $processor   Charitable donation processor.
	 * @return bool|array
	 */
	public static function process_donation( $return, $donation_id, $processor ) {
		return self::pronamic_process_donation( $return, $donation_id, $processor, new self() );
	}

	/**
	 * Returns the current gateway's ID.
	 *
	 * @return string
	 * @since  2.3.0
	 */
	public static function get_gateway_id() {
		return self::ID;
	}
}
