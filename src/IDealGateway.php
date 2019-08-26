<?php

namespace Pronamic\WordPress\Pay\Extensions\Charitable;

use Charitable_Donation_Processor;
use Charitable_Gateway;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Plugin;

/**
 * Title: Charitable iDEAL gateway
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class IDealGateway extends Gateway {
	/**
	 * The unique ID of this payment gateway
	 *
	 * @var string
	 */
	const ID = 'pronamic_pay_ideal';

	/**
	 * Constructs and initialize an iDEAL gateway
	 */
	public function __construct() {
		parent::__construct();

		$this->name = __( 'iDEAL', 'pronamic_ideal' );

		$this->defaults = array(
			'label' => __( 'iDEAL', 'pronamic_ideal' ),
		);

		$this->payment_method = PaymentMethods::IDEAL;
	}

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
	 * Form gateway fields.
	 *
	 * @see   https://github.com/Charitable/Charitable/blob/1.4.5/includes/donations/class-charitable-donation-form.php#L387
	 * @since 1.0.2
	 *
	 * @param array              $fields  Fields.
	 * @param Charitable_Gateway $gateway Gateway.
	 *
	 * @return array
	 */
	public static function form_gateway_fields( $fields, $gateway ) {
		if ( get_class() !== get_class( $gateway ) ) {
			return $fields;
		}

		$payment_method = $gateway->payment_method;

		$config_id = $gateway->get_value( 'config_id' );

		$gateway = Plugin::get_gateway( $config_id );

		if ( $gateway ) {
			$gateway->set_payment_method( $payment_method );

			$fields['pronamic-pay-input-html'] = array(
				'type'    => '',
				'gateway' => $gateway,
			);
		}

		return $fields;
	}

	/**
	 * Form gateway field template.
	 *
	 * @see   https://github.com/Charitable/Charitable/blob/1.4.5/includes/abstracts/class-charitable-form.php#L231-L232
	 * @since 1.0.2
	 *
	 * @param false|Charitable_Template $template False by default.
	 * @param array                     $field    Field definition.
	 * @param Charitable_Form           $form     The Charitable_Form object.
	 * @param int                       $index    The current index.
	 *
	 * @return string
	 */
	public static function form_field_template( $template, $field, $form, $index ) {
		if ( 'pronamic-pay-input-html' === $field['key'] ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $field['gateway']->get_input_html();

			return;
		}

		return $template;
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
