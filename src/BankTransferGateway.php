<?php
/**
 * Bank transfer gateway.
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
 * Title: Charitable Bank Transfer gateway
 * Description:
 * Copyright: 2005-2024 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
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
	 * Payment method.
	 *
	 * @var string
	 */
	public $payment_method = PaymentMethods::BANK_TRANSFER;
}
