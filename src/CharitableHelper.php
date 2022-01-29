<?php
/**
 * Charitable Helper
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\Charitable
 */

namespace Pronamic\WordPress\Pay\Extensions\Charitable;

use Charitable_Donation;
use Charitable_Gateway;
use Pronamic\WordPress\Pay\AddressHelper;
use Pronamic\WordPress\Pay\ContactNameHelper;
use Pronamic\WordPress\Pay\CustomerHelper;
use Pronamic\WordPress\Pay\Subscriptions\Subscription;
use Pronamic\WordPress\Pay\Subscriptions\SubscriptionInterval;
use Pronamic\WordPress\Pay\Subscriptions\SubscriptionPhase;

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
	 * @param int $donation_id Donation ID.
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
	 * @param Charitable_Gateway $gateway     Charitable gateway.
	 * @param int                $donation_id Donation ID.
	 * @return string
	 */
	public static function get_description( $gateway, $donation_id ) {
		$description = $gateway->get_value( 'transaction_description' );
		$donation    = new Charitable_Donation( $donation_id );

		if ( '' === $description ) {
			$description = self::get_title( $donation_id );
		}

		// Replacements.
		$campaigns = $donation->get_campaigns();

		$replacements = array(
			'{donation_id}'         => $donation_id,
			'{first_campaign_name}' => reset( $campaigns ),
			'{campaign_name}'       => implode( ', ', $campaigns ),
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
	 * @param array<string, mixed> $user_data User data.
	 * @param string               $key       Array key.
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
	 *
	 * @param array<string, mixed> $user_data User data.
	 * @return \Pronamic\WordPress\Pay\Customer|null
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
	 *
	 * @param array<string, mixed> $user_data User data.
	 * @return \Pronamic\WordPress\Pay\ContactName|null
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
	 *
	 * @param array<string, mixed> $user_data User data.
	 * @return \Pronamic\WordPress\Pay\Address|null
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

	/**
	 * Get subscription.
	 *
	 * @return Subscription|null
	 */
	public static function get_subscription( $processor, $subscription_source_id, $description, $amount ) {
		if ( ! \class_exists( '\Charitable_Recurring' ) ) {
			return null;
		}

		// Monthly, yearly etc
		$donation_period = $processor->get_donation_data_value( 'donation_period', false );

		$recurring_id = $subscription_source_id;

		if ( empty( $donation_period ) || empty( $recurring_id ) ) {
			return null;
		}

		// Number of payments
		$donation_length = $processor->get_donation_data_value( 'donation_length', '' );
		$donation_length = empty( $donation_length ) ? '5200' : $donation_length;

		// duration between two $donation_period
		$donation_interval = $processor->get_donation_data_value( 'donation_interval', 1 );
		$recurring_key     = $processor->get_donation_data_value( 'recurring_donation_key' );
		$donation_key      = $processor->get_donation_data_value( 'donation_key' );

		// Convert period strings into  duration format.
		// link: https://www.php.net/manual/en/dateinterval.construct.php
		switch ( strtolower( $donation_period ) ) {
			case 'day':
				$donation_period = 'D';
				$donation_length = min( $donation_length, '36500' );
				break;
			case 'week':
				$donation_period = 'W';
				$donation_length = min( $donation_length, '5200' );
				break;
			case 'quarter':
				$donation_period   = 'M';
				$donation_length   = min( $donation_length, '400' );
				$donation_interval = $donation_interval * 3;
				break;
			case 'semiannual':
				$donation_period   = 'M';
				$donation_length   = min( $donation_length, '200' );
				$donation_interval = $donation_interval * 6;
				break;
			case 'month':
				$donation_period = 'M';
				$donation_length = min( $donation_length, '1200' );
				break;
			case 'year':
				$donation_period = 'Y';
				$donation_length = min( $donation_length, '100' );
				break;
			default:
				return null;
		}

		// Subscription.
		$subscription = new Subscription();

		$subscription->set_description( $description );

		// Phase.
		$phase = new SubscriptionPhase(
			$subscription,
			new \DateTimeImmutable(),
			new SubscriptionInterval( 'P' . $donation_interval . $donation_period ),
			$amount
		);

		$phase->set_total_periods( intval( $donation_length ) );

		$subscription->add_phase( $phase );

		return $subscription;
	}
}
