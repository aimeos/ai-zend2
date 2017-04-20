<?php

namespace Aimeos\Perf\Config;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2016
 */
class Zend2Test extends \PHPUnit\Framework\TestCase
{
	public function testZend2()
	{
		if( class_exists( 'Zend\Config' ) === false ) {
			$this->markTestSkipped( 'Class Zend\Config not found' );
		}


		$start = microtime( true );

		$paths = array(
			dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'one',
			dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'two',
		);

		for( $i = 0; $i < 1000; $i++ )
		{
			$conf = new \Aimeos\MW\Config\Zend( new Zend\Config\Config( [], true ), $paths );

			$conf->get( 'test/db/host' );
			$conf->get( 'test/db/username' );
			$conf->get( 'test/db/password' );
		}

		$stop = microtime( true );
		echo "\n    config zend2: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}
}
