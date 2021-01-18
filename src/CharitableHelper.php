<?php
/**
 * Charitable Helper
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2021 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\Charitable
 */

namespace Pronamic\WordPress\Pay\Extensions\Charitable;

use Charitable_Donation;
use Charitable_Donation_Processor;
use Charitable_Gateway;
use Pronamic\WordPress\Money\Parser as MoneyParser;
use Pronamic\WordPress\Pay\Address;
use Pronamic\WordPress\Pay\AddressHelper;
use Pronamic\WordPress\Pay\ContactName;
use Pronamic\WordPress\Pay\ContactNameHelper;
use Pronamic\WordPress\Pay\Customer;
use Pronamic\WordPress\Pay\CustomerHelper;

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
		return \sprintf(
			/* translators: %s: Charitable donation ID */
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
	 * @link https://github.com/Charitable/Charitable/blob/1.6.46/includes/abstracts/abstract-class-charitable-abstract-donation.php#L271-L287
	 * @param int $donation_id Donation ID.
	 * @return float
	 */
	public static function get_total_amount_value( $donation_id ) {
		$donation = new Charitable_Donation( $donation_id );

		return $donation->get_total_donation_amount( true );
	}

	/**
	 * Get value from user data.
	 *
	 * @param array $user_data User data.
	 * @param string $key      Array key.
	 * @return null|string
	 */
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
		return CustomerHelper::from_array(
			array(
				'name'    => self::get_name_from_user_data( $user_data ),
				'email'   => self::get_value_from_user_data( $user_data, 'email' ),
				'phone'   => self::get_value_from_user_data( $user_data, 'phone' ),
				'user_id' => null,
			)
		);
	}

	/**
	 * Get name from user data.
	 */
	public static function get_name_from_user_data( $user_data ) {
		return ContactNameHelper::from_array(
			array(
				'first_name' => self::get_value_from_user_data( $user_data, 'first_name' ),
				'last_name'  => self::get_value_from_user_data( $user_data, 'last_name' ),
			)
		);
	}

	/**
	 * Get address from user data.
	 */
	public static function get_address_from_user_data( $user_data ) {
		return AddressHelper::from_array(
			array(
				'name'         => self::get_name_from_user_data( $user_data ),
				'line_1'       => self::get_value_from_user_data( $user_data, 'address' ),
				'line_2'       => self::get_value_from_user_data( $user_data, 'address_2' ),
				'postal_code'  => self::get_value_from_user_data( $user_data, 'postcode' ),
				'city'         => self::get_value_from_user_data( $user_data, 'city' ),
				'region'       => self::get_value_from_user_data( $user_data, 'state' ),
				'country_code' => self::get_value_from_user_data( $user_data, 'country' ),
				'email'        => self::get_value_from_user_data( $user_data, 'email' ),
				'phone'        => self::get_value_from_user_data( $user_data, 'phone' ),
			)
		);
	}
}
