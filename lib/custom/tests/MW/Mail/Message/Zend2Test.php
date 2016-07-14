<?php

namespace Aimeos\MW\Mail\Message;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 */
class Zend2Test extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $mock;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		if( !class_exists( 'Zend\Mail\Message' ) ) {
			$this->markTestSkipped( 'Zend\Mail\Message is not available' );
		}

		$this->mock = $this->getMockBuilder( 'Zend\Mail\Message' )->getMock();
		$this->object = new \Aimeos\MW\Mail\Message\Zend2( $this->mock, 'UTF-8' );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
	}


	public function testAddFrom()
	{
		$this->mock->expects( $this->once() )->method( 'setFrom' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->object->addFrom( 'a@b', 'test' );
		$this->assertSame( $this->object, $result );
	}


	public function testAddTo()
	{
		$this->mock->expects( $this->once() )->method( 'addTo' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->object->addTo( 'a@b', 'test' );
		$this->assertSame( $this->object, $result );
	}


	public function testAddCc()
	{
		$this->mock->expects( $this->once() )->method( 'addCc' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->object->addCc( 'a@b', 'test' );
		$this->assertSame( $this->object, $result );
	}


	public function testAddBcc()
	{
		$this->mock->expects( $this->once() )->method( 'addBcc' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->object->addBcc( 'a@b', 'test' );
		$this->assertSame( $this->object, $result );
	}


	public function testAddReplyTo()
	{
		$this->mock->expects( $this->once() )->method( 'setReplyTo' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->object->addReplyTo( 'a@b', 'test' );
		$this->assertSame( $this->object, $result );
	}


	public function testAddHeader()
	{
		$mockHeader = $this->getMockBuilder( 'Zend\Mail\Headers' )->getMock();

		$mockHeader->expects( $this->once() )->method( 'addHeaderLine' )
			->with( $this->stringContains( 'test' ), $this->stringContains( 'value' ) );

		$this->mock->expects( $this->once() )->method( 'getHeaders' )
			->will( $this->returnValue( $mockHeader ) );

		$result = $this->object->addHeader( 'test', 'value' );
		$this->assertSame( $this->object, $result );
	}


	public function testSetSender()
	{
		$this->mock->expects( $this->once() )->method( 'setFrom' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->object->setSender( 'a@b', 'test' );
		$this->assertSame( $this->object, $result );
	}


	public function testSetSubject()
	{
		$this->mock->expects( $this->once() )->method( 'setSubject' )
			->with( $this->stringContains( 'test' ) );

		$result = $this->object->setSubject( 'test' );
		$this->assertSame( $this->object, $result );
	}


	public function testSetBody()
	{
		$result = $this->object->setBody( 'test' );
		$this->object->getObject();

		$this->assertSame( $this->object, $result );
	}


	public function testSetBodyHtml()
	{
		$result = $this->object->setBodyHtml( 'test' );
		$this->object->getObject();

		$this->assertSame( $this->object, $result );
	}


	public function testAddAttachment()
	{
		$result = $this->object->addAttachment( 'test', 'text/plain', 'test.txt', 'inline' );
		$this->assertSame( $this->object, $result );
	}


	public function testEmbedAttachment()
	{
		$result = $this->object->embedAttachment( 'test', 'text/plain', 'test.txt' );
		$this->object->getObject();

		$this->assertInternalType( 'string', $result );
	}


	public function testEmbedAttachmentMultiple()
	{
		$object = new \Aimeos\MW\Mail\Message\Zend2( new \Zend\Mail\Message() );

		$object->setBody( 'text body' );
		$object->embedAttachment( 'test', 'text/plain', 'test.txt' );
		$object->embedAttachment( 'test', 'text/plain', 'test.txt' );

		$exp = '#Content-Disposition: inline; filename="test.txt".*Content-Disposition: inline; filename="1_test.txt"#smu';
		$this->assertRegExp( $exp, $object->getObject()->toString() );
	}


	public function testGetObject()
	{
		$this->assertInstanceOf( 'Zend\Mail\Message', $this->object->getObject() );
	}


	public function testGenerateMailAlternative()
	{
		$object = new \Aimeos\MW\Mail\Message\Zend2( new \Zend\Mail\Message() );

		$object->setBody( 'text body' );
		$object->setBodyHtml( 'html body' );

		$exp = '#Content-Type: multipart/alternative.*Content-Type: text/plain.*Content-Type: text/html#smu';
		$this->assertRegExp( $exp, $object->getObject()->toString() );
	}


	public function testGenerateMailRelated()
	{
		$object = new \Aimeos\MW\Mail\Message\Zend2( new \Zend\Mail\Message() );

		$object->embedAttachment( 'embedded-data', 'text/plain', 'embedded.txt' );
		$object->setBodyHtml( 'html body' );

		$exp = '#Content-Type: multipart/related.*Content-Type: text/html.*Content-Type: text/plain#smu';
		$this->assertRegExp( $exp, $object->getObject()->toString() );
	}


	public function testGenerateMailFull()
	{
		$object = new \Aimeos\MW\Mail\Message\Zend2( new \Zend\Mail\Message() );

		$object->addAttachment( 'attached-data', 'text/plain', 'attached.txt' );
		$object->embedAttachment( 'embedded-data', 'text/plain', 'embedded.txt' );
		$object->setBodyHtml( 'html body' );
		$object->setBody( 'text body' );

		$exp = '#Content-Type: multipart/mixed.*Content-Type: multipart/alternative.*Content-Type: text/plain.*Content-Type: multipart/related.*Content-Type: text/html.*Content-Type: text/plain.*Content-Type: text/plain#smu';
		$this->assertRegExp( $exp, $object->getObject()->toString() );
	}

}
