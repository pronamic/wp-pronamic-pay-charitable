<?php

/**
 * Title: WordPress pay Charitable test
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 * @since 1.0.0
 */
class Pronamic_WP_Pay_Extensions_Charitable_CharitableTest {
	/**
	 * Test.
	 */
	public function test() {
		$this->assertTrue( class_exists( 'Pronamic_WP_Pay_Extensions_Charitable_Charitable' ) );
	}
}
