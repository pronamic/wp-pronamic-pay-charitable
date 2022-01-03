<?php
/**
 * Sofort gateway.
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
 * Title: Charitable Sofort gateway
 * Description:
 * Copyright: 2005-2022 Pronamic
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
	 * Payment method.
	 *
	 * @var string
	 */
	protected $payment_method = PaymentMethods::SOFORT;

	/**
	 * Process donation.
	 *
	 * @since   1.0.2
	 *
	 * @param bool|array                    $return      Return.
	 * @param int                           $donation_id Donation ID.
	 * @param Charitable_Donation_Processor $processor   Charitable donation processor.
	 *
	 * @return bool|array
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
