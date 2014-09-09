<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MW
 * @subpackage Mail
 */


/**
 * Zend implementation for creating and sending e-mails.
 *
 * @package MW
 * @subpackage Mail
 */
class MW_Mail_Zend2 implements MW_Mail_Interface
{
	private $_transport;


	/**
	 * Initializes the instance of the class.
	 *
	 * @param Zend_Mail_Transport_Abstract $transport Mail transport object
	 */
	public function __construct( Zend\Mail\Transport\TransportInterface $transport )
	{
		$this->_transport = $transport;
	}


	/**
	 * Creates a new e-mail message object.
	 *
	 * @param string $charset Default charset of the message
	 * @return MW_Mail_Message_Interface E-mail message object
	 */
	public function createMessage( $charset = 'UTF-8' )
	{
		return new MW_Mail_Message_Zend2( new Zend\Mail\Message(), $charset );
	}


	/**
	 * Sends the e-mail message to the mail server.
	 *
	 * @param MW_Mail_Message_Interface $message E-mail message object
	 */
	public function send( MW_Mail_Message_Interface $message )
	{
		$this->_transport->send( $message->getObject() );
	}


	/**
	 * Clones the internal objects.
	 */
	public function __clone()
	{
		$this->_object = clone $this->_object;
		$this->_transport = clone $this->_transport;
	}
}
