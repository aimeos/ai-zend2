<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 * @package MW
 * @subpackage Logger
 */


/**
 * Log messages using Zend\Log.
 *
 * @package MW
 * @subpackage Logger
 */
class MW_Logger_Zend2 extends MW_Logger_Abstract implements MW_Logger_Interface
{
	private $logger = null;


	/**
	 * Initializes the logger object.
	 *
	 * @param Zend\Log\Logger $logger Zend logger object
	 */
	public function __construct( Zend\Log\Logger $logger )
	{
		$this->logger = $logger;
	}


	/**
	 * Writes a message to the configured log facility.
	 *
	 * @param string $message Message text that should be written to the log facility
	 * @param integer $priority Priority of the message for filtering
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @throws MW_Logger_Exception If an error occurs in Zend_Log
	 * @see MW_Logger_Abstract for available log level constants
	 */
	public function log( $message, $priority = MW_Logger_Abstract::ERR, $facility = 'message' )
	{
		try
		{
			if( !is_scalar( $message ) ) {
				$message = json_encode( $message );
			}

			$this->logger->log( $priority, '<' . $facility . '> ' . $message );
		}
		catch( Zend\Log\Exception\InvalidArgumentException $ze )	{
			throw new MW_Logger_Exception( $ze->getMessage() );
		}
		catch( Zend\Log\Exception\RuntimeException $ze )	{
			throw new MW_Logger_Exception( $ze->getMessage() );
		}
	}
}