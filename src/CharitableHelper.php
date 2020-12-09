<?php
/**
 * Charitable Helper
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\Charitable
 */

namespace Pronamic\WordPress\Pay\Extensions\Charitable;

use Charitable_Donation;
use Charitable_Donation_Processor;
use Charitable_Gateway;
use Pronamic\WordPress\Money\Parser as MoneyParser;
use Pronamic\WordPress\Pay\Address;
use Pronamic\WordPress\Pay\ContactName;
use Pronamic\WordPress\Pay\Customer
;

/**
 * Charitable Helper
 *
 * @version 2.2.0
 * @since   2.2.0
 */
class CharitableHelper {
	/**
	 * Get description.
	 *
	 * @return string
	 */
	public static function get_title( $donation_id ) {
		/* translators: %s: Charitable donation ID */
		return \sprintf(
			\__( 'Charitable donation %s', 'pronamic_ideal' ),
			$donation_id
		);
	}

	/**
	 * Get description.
	 *
	 * @return string
	 */
	public static function get_description( $gateway, $donation_id ) {
		$description = $gateway->get_value( 'transaction_description' );

		if ( '' === $description ) {
			$description = self::get_title( $donation_id );
		}

		// Replacements.
		$replacements = array(
			'{donation_id}' => $donation_id,
		);

		return \strtr( $description, $replacements );
	}

	/**
	 * Get total amount value.
	 *
	 * @param int $donation_id Donation ID.
	 * @return float
	 */
	public static function get_total_amount_value( $donation_id ) {
		$donation = new Charitable_Donation( $donation_id );

		// Price.
		$money_parser = new MoneyParser();

		return $money_parser->parse( $donation->get_total_donation_amount() )->get_value();
	}

	public static function get_value_from_user_data( $user_data, $key ) {
		if ( ! array_key_exists( $key, $user_data ) ) {
			return null;
		}

		return $user_data[ $key ];
	}

	/**
	 * Get customer from user data.
	 */
	public static function get_customer_from_user_data( $user_data ) {
		$name    = self::get_name_from_user_data( $user_data );
		$email   = self::get_value_from_user_data( $user_data, 'email' );
		$phone   = self::get_value_from_user_data( $user_data, 'phone' );
		$user_id = null;

		$customer_data = array(
			$name,
			$email,
			$phone,
			$user_id,
		);

		$customer_data = \array_filter( $customer_data );

		if ( empty( $customer_data ) ) {
			return null;
		}

		$customer = new Customer();

		$customer->set_name( $name );

		if ( ! empty( $email ) ) {
			$customer->set_email( $email );
		}

		if ( ! empty( $phone ) ) {
			$customer->set_phone( $phone );
		}

		if ( ! empty( $user_id ) ) {
			$customer->set_user_id( \intval( $user_id ) );
		}

		return $customer;
	}

	/**
	 * Get name from user data.
	 */
	public static function get_name_from_user_data( $user_data ) {
		$first_name = self::get_value_from_user_data( $user_data, 'first_name' );
		$last_name  = self::get_value_from_user_data( $user_data, 'last_name' );

		$name_data = array(
			$first_name,
			$last_name,
		);

		$name_data = \array_filter( $name_data );

		if ( empty( $name_data ) ) {
			return null;
		}

		$name = new ContactName();

		if ( ! empty( $first_name ) ) {
			$name->set_first_name( $first_name );
		}

		if ( ! empty( $last_name ) ) {
			$name->set_last_name( $last_name );
		}
		
		return $name;
	}

	/**
	 * Get address from user data.
	 */
	public static function get_address_from_user_data( $user_data ) {
		$name         = self::get_name_from_user_data( $user_data );
		$line_1       = self::get_value_from_user_data( $user_data, 'address' );
		$line_2       = self::get_value_from_user_data( $user_data, 'address_2' );
		$postal_code  = self::get_value_from_user_data( $user_data, 'postcode' );
		$city         = self::get_value_from_user_data( $user_data, 'city' );
		$state        = self::get_value_from_user_data( $user_data, 'state' );
		$country_code = self::get_value_from_user_data( $user_data, 'country' );
		$email        = self::get_value_from_user_data( $user_data, 'email' );
		$phone        = self::get_value_from_user_data( $user_data, 'phone' );

		$address_data = array(
			$name,
			$line_1,
			$line_2,
			$postal_code,
			$city,
			$state,
			$country_code,
			$email,
			$phone,
		);

		$address_data = array_filter( $address_data );

		if ( empty( $address_data ) ) {
			return;
		}

		$address = new Address();

		if ( ! empty( $name ) ) {
			$address->set_name( $name );
		}

		if ( ! empty( $line_1 ) ) {
			$address->set_line_1( $line_1 );
		}

		if ( ! empty( $line_2 ) ) {
			$address->set_line_2( $line_2 );
		}

		if ( ! empty( $postal_code ) ) {
			$address->set_postal_code( $postal_code );
		}

		if ( ! empty( $city ) ) {
			$address->set_city( $city );
		}

		if ( ! empty( $state ) ) {
			$address->set_region( $state );
		}

		if ( ! empty( $country_code ) ) {
			$address->set_country_code( $country_code );
		}

		if ( ! empty( $email ) ) {
			$address->set_email( $email );
		}

		if ( ! empty( $phone ) ) {
			$address->set_phone( $phone );
		}

		return $address;
	}
}
