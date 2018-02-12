<?php

namespace Pronamic\WordPress\Pay\Extensions\Charitable;

use Charitable_Donation;
use Pronamic\WordPress\Pay\Payments\PaymentData as Pay_PaymentData;
use Pronamic\WordPress\Pay\Payments\Item;
use Pronamic\WordPress\Pay\Payments\Items;

/**
 * Title: WordPress pay Charitable payment data
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 1.1.2
 * @since   1.0.0
 */
class PaymentData extends Pay_PaymentData {
	/**
	 * The donation ID
	 */
	private $donation_id;

	/**
	 * Processor
	 */
	private $processor;

	/**
	 * Gateway
	 */
	private $gateway;

	/**
	 * User data
	 */
	private $user_data;

	//////////////////////////////////////////////////

	/**
	 * Constructs and initializes an Charitable payment data object.
	 *
	 * @param       $donation_id
	 * @param mixed $processor
	 * @param       $gateway
	 */
	public function __construct( $donation_id, $processor, $gateway ) {
		parent::__construct();

		$this->donation_id = $donation_id;
		$this->processor   = $processor;
		$this->gateway     = $gateway;

		$this->user_data = $processor->get_donation_data_value( 'user' );
	}

	//////////////////////////////////////////////////

	/**
	 * Get source indicator
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_source()
	 * @return string
	 */
	public function get_source() {
		return 'charitable';
	}

	public function get_source_id() {
		return $this->donation_id;
	}

	//////////////////////////////////////////////////

	public function get_title() {
		/* translators: %s: order id */
		return sprintf( __( 'Charitable donation %s', 'pronamic_ideal' ), $this->get_order_id() );
	}

	/**
	 * Get description
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_description()
	 * @return string
	 */
	public function get_description() {
		$description = $this->gateway->get_value( 'transaction_description' );

		if ( '' === $description ) {
			$description = $this->get_title();
		}

		// Replacements
		$replacements = array(
			'{donation_id}' => $this->get_order_id(),
		);

		return strtr( $description, $replacements );
	}

	/**
	 * Get order ID
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_order_id()
	 * @return string
	 */
	public function get_order_id() {
		return $this->donation_id;
	}

	/**
	 * Get items
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_items()
	 * @return Items
	 */
	public function get_items() {
		$donation = new Charitable_Donation( $this->donation_id );

		// Items
		$items = new Items();

		// Item
		// We only add one total item, because iDEAL cant work with negative price items (discount)
		$item = new Item();
		$item->setNumber( $this->get_order_id() );
		$item->setDescription( $this->get_description() );
		// @see http://plugins.trac.wordpress.org/browser/woocommerce/tags/1.5.2.1/classes/class-wc-order.php#L50
		$item->setPrice( $donation->get_total_donation_amount() );
		$item->setQuantity( 1 );

		$items->addItem( $item );

		return $items;
	}

	//////////////////////////////////////////////////
	// Currency
	//////////////////////////////////////////////////

	/**
	 * Get currency
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_currency_alphabetic_code()
	 * @return string
	 */
	public function get_currency_alphabetic_code() {
		return charitable_get_currency();
	}

	//////////////////////////////////////////////////
	// Customer
	//////////////////////////////////////////////////

	public function get_email() {
		return $this->user_data['email'];
	}

	public function get_first_name() {
		if ( isset( $this->user_data['first_name'] ) ) {
			return $this->user_data['first_name'];
		}
	}

	public function get_last_name() {
		if ( isset( $this->user_data['last_name'] ) ) {
			return $this->user_data['last_name'];
		}
	}

	public function get_customer_name() {
		return $this->user_data['first_name'] . ' ' . $this->user_data['last_name'];
	}

	public function get_address() {
		return $this->user_data['address'];
	}

	public function get_city() {
		return $this->user_data['city'];
	}

	public function get_zip() {
		return $this->user_data['postcode'];
	}

	//////////////////////////////////////////////////
	// URL's
	//////////////////////////////////////////////////

	/**
	 * Get normal return URL.
	 *
	 * @see https://github.com/woothemes/woocommerce/blob/v2.1.3/includes/abstracts/abstract-wc-payment-gateway.php#L52
	 * @return string
	 */
	public function get_normal_return_url() {
		return charitable_get_permalink( 'donation_receipt_page', array( 'donation_id' => $this->donation_id ) );
	}

	public function get_cancel_url() {
		$cancel_url = charitable_get_permalink( 'donation_cancel_page', array( 'donation_id' => $this->donation_id ) );

		if ( ! $cancel_url ) {
			$cancel_url = esc_url_raw( add_query_arg( array(
				'donation_id' => $this->donation_id,
				'cancel'      => true,
			), wp_get_referer() ) );
		}

		return $cancel_url;
	}

	public function get_success_url() {
		return charitable_get_permalink( 'donation_receipt_page', array( 'donation_id' => $this->donation_id ) );
	}

	public function get_error_url() {
		$cancel_url = charitable_get_permalink( 'donation_cancel_page', array( 'donation_id' => $this->donation_id ) );

		if ( ! $cancel_url ) {
			$cancel_url = esc_url_raw( add_query_arg( array(
				'donation_id' => $this->donation_id,
				'cancel'      => true,
			), wp_get_referer() ) );
		}

		return $cancel_url;
	}
}
