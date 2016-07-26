<?php

namespace Sms;

class Message extends AbstractMessage implements MessageInterface
{
	const HEADER_PARAMETERS = [
		'To_TOA'         => [ 'type' => '' ],
		'Flash'          => [ 'type' => '' ],
		'Alphabet'       => [ 'type' => '' ],
		'UDH'            => [ 'type' => '' ],
		'UDH-DATA'       => [ 'type' => '' ],
		'SMSC'           => [ 'type' => '' ],
		'Provider Queue' => [ 'type' => '' ],
		'Report'         => [ 'type' => '' ],
		'Autosplit'      => [ 'type' => '' ],
		'Priority'       => [ 'type' => '' ],
		'Validity'       => [ 'type' => '' ],
		'Voicecall'      => [ 'type' => '' ],
		'Hex'            => [ 'type' => '' ],
		'Replace'        => [ 'type' => '' ],
		'Include'        => [ 'type' => '' ],
		'Macro'          => [ 'type' => '' ],
		'System_message' => [ 'type' => '' ]
	];

	/**
	 * Message body
	 *
	 * @var string
	 */
	protected $body;

	/**
	 * Who to send the message to
	 *
	 * @var string
	 */
	protected $to;

	/**
	 * Optional headers
	 *
	 * @var \stdClass|null
	 */
	protected $headers;

	/**
	 * Class constructor
	 *
	 * @param string     $body
	 * @param string     $to
	 * @param array|null $optHeaders
	 *
	 * @return  Sms\Message
	 */
	public function __construct($body, $to, array $optHeaders = null)
	{
		$this->setBody($body);
		$this->setTo($to);

		if (!is_null($optHeaders)) {
			foreach ($optHeaders as $header => $value) {
				$this->setHeader($header, $value);
			}
		}
	}

	/**
	 * Set To
	 *
	 * @param string $to
	 *
	 * @throws  Sms\SmsMessageException
	 */
	private function setTo($to = "")
	{
		$this->to = $to;
	}

	/**
	 * Flatten this down to a string, suitable for sending
	 *
	 * @return string
	 */
	public function flatten()
	{
		$str = "To: {$this->to}\n";

		if (!is_null($this->headers)) {
			foreach ($this->headers as $header => $content) {
				$str .= "{$header}: {$content}\n";
			}
		}

		return $str . "\n{$this->body}";
	}
}
