<?php
/**
 * Charitable Dependency
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\Charitable
 */

namespace Pronamic\WordPress\Pay\Extensions\Charitable;

use Pronamic\WordPress\Pay\Dependencies\Dependency;

/**
 * Charitable Dependency
 *
 * @author  Reüel van der Steege
 * @version 2.1.1
 * @since   2.1.0
 */
class CharitableDependency extends Dependency {
	/**
	 * Is met.
	 *
	 * @link
	 * @return bool True if dependency is met, false otherwise.
	 */
	public function is_met() {
		if ( ! \class_exists( '\Charitable' ) ) {
			return false;
		}

		return true;
	}
}
