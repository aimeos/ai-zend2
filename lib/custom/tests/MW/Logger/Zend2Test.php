<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2018
 */


namespace Aimeos\MW\Logger;


/**
 * Test class for \Aimeos\MW\Logger\Zend2.
 */
class Zend2Test extends \PHPUnit\Framework\TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		if( class_exists( 'Zend\Log\Logger' ) === false ) {
			$this->markTestSkipped( 'Class Zend\Log\Logger not found' );
		}

		$writer = new \Zend\Log\Writer\Stream( 'error.log' );

		$formatter = new \Zend\Log\Formatter\Simple( 'log: %message%' . PHP_EOL );
		$writer->setFormatter( $formatter );

		$filter = new \Zend\Log\Filter\Priority( \Zend\Log\Logger::INFO );
		$writer->addFilter( $filter );

		$logger = new \Zend\Log\Logger();
		$logger->addWriter( $writer );

		$this->object = new \Aimeos\MW\Logger\Zend2( $logger );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unlink( 'error.log' );
	}


	public function testLog()
	{
		$this->object->log( 'error' );
		$this->assertEquals( "log: <message> error\n\n", file_get_contents( 'error.log' ) );
	}


	public function testNonScalarLog()
	{
		$this->object->log( array( 'error', 'error2', 2 ) );
		$this->assertEquals( 'log: <message> ["error","error2",2]' . "\n\n", file_get_contents( 'error.log' ) );
	}


	public function testLogDebug()
	{
		$this->object->log( 'debug', \Aimeos\MW\Logger\Base::DEBUG );
		$this->assertEquals( '', file_get_contents( 'error.log' ) );
	}


	public function testBadPriority()
	{
		$this->setExpectedException( '\\Aimeos\\MW\\Logger\\Exception' );
		$this->object->log( 'error', -1 );
	}
}
