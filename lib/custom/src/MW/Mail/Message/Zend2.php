<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 * @package MW
 * @subpackage Mail
 */


/**
 * Zend implementation for creating e-mails.
 *
 * @package MW
 * @subpackage Mail
 */
class MW_Mail_Message_Zend2 implements MW_Mail_Message_Interface
{
	private $html;
	private $text;
	private $object;
	private $charset;
	private $attach = array();
	private $embedded = array();


	/**
	 * Initializes the message instance.
	 *
	 * @param Zend\Mail\Message $object Zend mail object
	 */
	public function __construct( Zend\Mail\Message $object, $charset = 'UTF-8' )
	{
		$this->object = $object;
	}


	/**
	 * Adds a source e-mail address of the message.
	 *
	 * @param string $email Source e-mail address
	 * @param string|null $name Name of the user sending the e-mail or null for no name
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function addFrom( $email, $name = null )
	{
		$this->object->setFrom( $email, $name );
		return $this;
	}


	/**
	 * Adds a destination e-mail address of the target user mailbox.
	 *
	 * @param string $email Destination address of the target mailbox
	 * @param string|null $name Name of the user owning the target mailbox or null for no name
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function addTo( $email, $name = null )
	{
		$this->object->addTo( $email, $name );
		return $this;
	}


	/**
	 * Adds a destination e-mail address for a copy of the message.
	 *
	 * @param string $email Destination address for a copy
	 * @param string|null $name Name of the user owning the target mailbox or null for no name
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function addCc( $email, $name = null )
	{
		$this->object->addCc( $email, $name );
		return $this;
	}


	/**
	 * Adds a destination e-mail address for a hidden copy of the message.
	 *
	 * @param string $email Destination address for a hidden copy
	 * @param string|null $name Name of the user owning the target mailbox or null for no name
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function addBcc( $email, $name = null )
	{
		$this->object->addBcc( $email, $name );
		return $this;
	}


	/**
	 * Adds the return e-mail address for the message.
	 *
	 * @param string $email E-mail address which should receive all replies
	 * @param string|null $name Name of the user which should receive all replies or null for no name
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function addReplyTo( $email, $name = null )
	{
		$this->object->setReplyTo( $email, $name );
		return $this;
	}


	/**
	 * Adds a custom header to the message.
	 *
	 * @param string $name Name of the custom e-mail header
	 * @param string $value Text content of the custom e-mail header
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function addHeader( $name, $value )
	{
		$this->object->getHeaders()->addHeaderLine( $name, $value );
		return $this;
	}


	/**
	 * Sets the e-mail address and name of the sender of the message (higher precedence than "From").
	 *
	 * @param string $email Source e-mail address
	 * @param string|null $name Name of the user who sent the message or null for no name
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function setSender( $email, $name = null )
	{
		$this->object->setFrom( $email, $name );
		return $this;
	}


	/**
	 * Sets the subject of the message.
	 *
	 * @param string $subject Subject of the message
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function setSubject( $subject )
	{
		$this->object->setSubject( $subject );
		return $this;
	}


	/**
	 * Sets the text body of the message.
	 *
	 * @param string $message Text body of the message
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function setBody( $message )
	{
		$part = new Zend\Mime\Part( $message );

		$part->charset = $this->charset;
		$part->encoding = Zend\Mime\Mime::ENCODING_QUOTEDPRINTABLE;
		$part->type = Zend\Mime\Mime::TYPE_TEXT;

		$this->text = $part;
		return $this;
	}


	/**
	 * Sets the HTML body of the message.
	 *
	 * @param string $message HTML body of the message
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function setBodyHtml( $message )
	{
		$part = new Zend\Mime\Part( $message );

		$part->charset = $this->charset;
		$part->encoding = Zend\Mime\Mime::ENCODING_QUOTEDPRINTABLE;
		$part->disposition = Zend\Mime\Mime::DISPOSITION_INLINE;
		$part->type = Zend\Mime\Mime::TYPE_HTML;

		$this->html = $part;
		return $this;
	}


	/**
	 * Adds an attachment to the message.
	 *
	 * @param string $data Binary or string
	 * @param string $mimetype Mime type of the attachment (e.g. "text/plain", "application/octet-stream", etc.)
	 * @param string|null $filename Name of the attached file (or null if inline disposition is used)
	 * @param string $disposition Type of the disposition ("attachment" or "inline")
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function addAttachment( $data, $mimetype, $filename, $disposition = 'attachment' )
	{
		$part = new Zend\Mime\Part( $data );

		$part->encoding = Zend\Mime\Mime::ENCODING_BASE64;
		$part->disposition = $disposition;
		$part->filename = $filename;
		$part->type = $mimetype;

		$this->attach[] = $part;

		return $this;
	}


	/**
	 * Embeds an attachment into the message and returns its reference.
	 *
	 * @param string $data Binary or string
	 * @param string $mimetype Mime type of the attachment (e.g. "text/plain", "application/octet-stream", etc.)
	 * @param string|null $filename Name of the attached file
	 * @return string Content ID for referencing the attachment in the HTML body
	 */
	public function embedAttachment( $data, $mimetype, $filename )
	{
		$cnt = 0;
		$newfile = $filename;

		while( isset( $this->embedded[$newfile] ) ) {
			$newfile = ++$cnt . '_' . $filename;
		}

		$part = new Zend\Mime\Part( $data );

		$part->disposition = Zend\Mime\Mime::DISPOSITION_INLINE;
		$part->encoding = Zend\Mime\Mime::ENCODING_BASE64;
		$part->filename = $newfile;
		$part->type = $mimetype;
		$part->id = md5( $newfile . mt_rand() );

		$this->embedded[$newfile] = $part;

		return 'cid:' . $part->id;
	}


	/**
	 * Returns the internal Zend mail message object.
	 *
	 * @return Zend\Mail\Message Zend mail message object
	 */
	public function getObject()
	{
		$msgparts = $parts = array();

		if( !empty( $this->embedded ) )
		{
			$type = Zend\Mime\Mime::MULTIPART_RELATED;

			if( $this->html != null ) {
				$parts[] = $this->createContainer( array_merge( array( $this->html ), $this->embedded ), $type );
			} else {
				$parts[] = $this->createContainer( $this->embedded, $type );
			}
		}
		else if( $this->html != null )
		{
			$parts[] = $this->html;
		}

		if( $this->text !== null ) {
			$parts[] = $this->text;
		}

		if( count( $parts ) === 2 )
		{
			$type = Zend\Mime\Mime::MULTIPART_ALTERNATIVE;
			$msgparts = array( $this->createContainer( array_reverse( $parts ), $type ) );
		}
		else if( !empty( $parts ) )
		{
			$msgparts = $parts;
		}

		$msg = new Zend\Mime\Message();
		$msg->setParts( array_merge( $msgparts, $this->attach ) );

		$this->object->setBody( $msg );

		return $this->object;
	}


	/**
	 * Clones the internal objects.
	 */
	public function __clone()
	{
		$this->object = clone $this->object;
	}


	/**
	 * Creates a mail message container of the given type for the mime parts.
	 *
	 * @param Zend\Mime\Part[] $parts List of mime parts that should be included in the container
	 * @param string $type Mime type, e.g. "multipart/related" or "multipart/alternative"
	 * @return \Zend\Mime\Part Container mime object
	 */
	protected function createContainer( array $parts, $type )
	{
		$msg = new Zend\Mime\Message();
		$msg->setParts( $parts );

		$part = new Zend\Mime\Part( $msg->generateMessage() );

		$part->encoding = Zend\Mime\Mime::ENCODING_8BIT;
		$part->boundary = $msg->getMime()->boundary();
		$part->disposition = null;
		$part->charset = null;
		$part->type = $type;

		return $part;
	}
}
