<?php

namespace Pronamic\WordPress\Pay\Extensions\Charitable;

use PHPUnit_Framework_TestCase;

/**
 * Title: WordPress pay Charitable test
 * Description:
 * Copyright: 2005-2023 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class CharitableTest extends PHPUnit_Framework_TestCase {
	/**
	 * Test class.
	 */
	public function test_class() {
		$this->assertTrue( class_exists( __NAMESPACE__ . '\Charitable' ) );
	}
}
