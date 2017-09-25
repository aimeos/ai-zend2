<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2017
 * @package MW
 * @subpackage Mail
 */


namespace Aimeos\MW\Mail;


/**
 * Zend implementation for creating and sending e-mails.
 *
 * @package MW
 * @subpackage Mail
 */
class Zend2 implements \Aimeos\MW\Mail\Iface
{
	private $transport;


	/**
	 * Initializes the instance of the class.
	 *
	 * @param \Zend\Mail\Transport\TransportInterface $transport Mail transport object
	 */
	public function __construct( \Zend\Mail\Transport\TransportInterface $transport )
	{
		$this->transport = $transport;
	}


	/**
	 * Creates a new e-mail message object.
	 *
	 * @param string $charset Default charset of the message
	 * @return \Aimeos\MW\Mail\Message\Iface E-mail message object
	 */
	public function createMessage( $charset = 'UTF-8' )
	{
		return new \Aimeos\MW\Mail\Message\Zend2( new \Zend\Mail\Message(), $charset );
	}


	/**
	 * Sends the e-mail message to the mail server.
	 *
	 * @param \Aimeos\MW\Mail\Message\Iface $message E-mail message object
	 */
	public function send( \Aimeos\MW\Mail\Message\Iface $message )
	{
		$this->transport->send( $message->getObject() );
	}


	/**
	 * Clones the internal objects.
	 */
	public function __clone()
	{
		$this->object = clone $this->object;
		$this->transport = clone $this->transport;
	}
}
