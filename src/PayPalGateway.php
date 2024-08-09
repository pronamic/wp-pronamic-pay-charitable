<?php
/**
 * PayPal gateway.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay
 */

namespace Pronamic\WordPress\Pay\Extensions\Charitable;

use Charitable_Donation_Processor;
use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Title: Charitable PayPal gateway
 * Description:
 * Copyright: 2005-2024 Pronamic
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
}
