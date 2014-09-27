<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 */


class MW_Mail_Message_Zend2Test extends MW_Unittest_Testcase
{
	private $_object;
	private $_mock;


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

		$this->_mock = $this->getMock( 'Zend\Mail\Message' );
		$this->_object = new MW_Mail_Message_Zend2( $this->_mock, 'UTF-8' );
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
		$this->_mock->expects( $this->once() )->method( 'setFrom' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->_object->addFrom( 'a@b', 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testAddTo()
	{
		$this->_mock->expects( $this->once() )->method( 'addTo' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->_object->addTo( 'a@b', 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testAddCc()
	{
		$this->_mock->expects( $this->once() )->method( 'addCc' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->_object->addCc( 'a@b', 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testAddBcc()
	{
		$this->_mock->expects( $this->once() )->method( 'addBcc' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->_object->addBcc( 'a@b', 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testAddReplyTo()
	{
		$this->_mock->expects( $this->once() )->method( 'setReplyTo' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->_object->addReplyTo( 'a@b', 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testAddHeader()
	{
		$mockHeader = $this->getMockBuilder( 'Zend\Mail\Headers' )->getMock();

		$mockHeader->expects( $this->once() )->method( 'addHeaderLine' )
			->with( $this->stringContains( 'test' ), $this->stringContains( 'value' ) );

		$this->_mock->expects( $this->once() )->method( 'getHeaders' )
			->will( $this->returnValue( $mockHeader ) );

		$result = $this->_object->addHeader( 'test', 'value' );
		$this->assertSame( $this->_object, $result );
	}


	public function testSetSender()
	{
		$this->_mock->expects( $this->once() )->method( 'setFrom' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->_object->setSender( 'a@b', 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testSetSubject()
	{
		$this->_mock->expects( $this->once() )->method( 'setSubject' )
			->with( $this->stringContains( 'test' ) );

		$result = $this->_object->setSubject( 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testSetBody()
	{
		$result = $this->_object->setBody( 'test' );
		$this->_object->getObject();

		$this->assertSame( $this->_object, $result );
	}


	public function testSetBodyHtml()
	{
		$result = $this->_object->setBodyHtml( 'test' );
		$this->_object->getObject();

		$this->assertSame( $this->_object, $result );
	}


	public function testAddAttachment()
	{
		$result = $this->_object->addAttachment( 'test', 'text/plain', 'test.txt', 'inline' );
		$this->assertSame( $this->_object, $result );
	}


	public function testEmbedAttachment()
	{
		$result = $this->_object->embedAttachment( 'test', 'text/plain', 'test.txt' );
		$this->_object->getObject();

		$this->assertInternalType( 'string', $result );
	}


	public function testEmbedAttachmentMultiple()
	{
		$object = new MW_Mail_Message_Zend2( new Zend\Mail\Message() );

		$object->setBody( 'text body' );
		$object->embedAttachment( 'test', 'text/plain', 'test.txt' );
		$object->embedAttachment( 'test', 'text/plain', 'test.txt' );

		$exp = '#Content-Disposition: inline; filename="test.txt".*Content-Disposition: inline; filename="1_test.txt"#smu';
		$this->assertRegExp( $exp, $object->getObject()->toString() );
	}


	public function testGetObject()
	{
		$this->assertInstanceOf( 'Zend\Mail\Message', $this->_object->getObject() );
	}


	public function testGenerateMailAlternative()
	{
		$object = new MW_Mail_Message_Zend2( new Zend\Mail\Message() );

		$object->setBody( 'text body' );
		$object->setBodyHtml( 'html body' );

		$exp = '#Content-Type: multipart/alternative.*Content-Type: text/plain.*Content-Type: text/html#smu';
		$this->assertRegExp( $exp, $object->getObject()->toString() );
	}


	public function testGenerateMailRelated()
	{
		$object = new MW_Mail_Message_Zend2( new Zend\Mail\Message() );

		$object->embedAttachment( 'embedded-data', 'text/plain', 'embedded.txt' );
		$object->setBodyHtml( 'html body' );

		$exp = '#Content-Type: multipart/related.*Content-Type: text/html.*Content-Type: text/plain#smu';
		$this->assertRegExp( $exp, $object->getObject()->toString() );
	}


	public function testGenerateMailFull()
	{
		$object = new MW_Mail_Message_Zend2( new Zend\Mail\Message() );

		$object->addAttachment( 'attached-data', 'text/plain', 'attached.txt' );
		$object->embedAttachment( 'embedded-data', 'text/plain', 'embedded.txt' );
		$object->setBodyHtml( 'html body' );
		$object->setBody( 'text body' );

		$exp = '#Content-Type: multipart/mixed.*Content-Type: multipart/alternative.*Content-Type: text/plain.*Content-Type: multipart/related.*Content-Type: text/html.*Content-Type: text/plain.*Content-Type: text/plain#smu';
		$this->assertRegExp( $exp, $object->getObject()->toString() );
	}

}
