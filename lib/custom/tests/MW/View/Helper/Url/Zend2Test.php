<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 */


namespace Aimeos\MW\View\Helper\Url;


/**
 * Test class for \Aimeos\MW\View\Helper\Url\Zend2.
 */
class Zend2Test extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $router;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		if( !class_exists( '\Zend\Mvc\Router\Http\Wildcard' ) ) {
			$this->markTestSkipped( '\Zend\Mvc\Router\Http\Wildcard is not available' );
		}

		$view = new \Aimeos\MW\View\Standard();
		$this->router = $this->getMockBuilder( '\Zend\Mvc\Router\Http\Wildcard' )->getMock();

		$this->object = new \Aimeos\MW\View\Helper\Url\Zend2( $view, $this->router, 'https://localhost:80' );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->object = null;
		$this->router = null;
	}


	public function testTransform()
	{
		$this->router->expects( $this->once() )->method( 'assemble' )
			->will( $this->returnValue( 'testurl' ) );

		$this->assertEquals( 'testurl', $this->object->transform() );
	}


	public function testTransformSlashes()
	{
		$this->router->expects( $this->once() )->method( 'assemble' )
			->will( $this->returnValue( 'testurl' ) );

		$this->assertEquals( 'testurl', $this->object->transform( null, null, null, array( 'test' => 'a/b' ) ) );
	}


	public function testTransformArrays()
	{
		$this->router->expects( $this->once() )->method( 'assemble' )
			->will( $this->returnValue( 'testurl' ) );

		$this->assertEquals( 'testurl', $this->object->transform( null, null, null, array( 'test' => array( 'a', 'b' ) ) ) );
	}


	public function testTransformTrailing()
	{
		$this->router->expects( $this->once() )->method( 'assemble' )
			->will( $this->returnValue( 'testurl' ) );

		$this->assertEquals( 'testurl', $this->object->transform( null, null, null, array(), array( 'a', 'b' ) ) );
	}


	public function testTransformAbsolute()
	{
		$this->router->expects( $this->once() )->method( 'assemble' )
			->will( $this->returnValue( '/testurl' ) );

		$options = array( 'absoluteUri' => true );
		$result = $this->object->transform( null, null, null, array(), array(), $options );
		$this->assertEquals( 'https://localhost:80/testurl', $result );
	}
}
