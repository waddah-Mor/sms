<?php

namespace Sms;

class Message extends AbstractMessage implements MessageInterface
{
	public function __construct($message, $to, array $optHeaders = null)
	{
		$this->setMessage($message);
		$this->setTo((array) $message);

		if (!is_null($optHeaders)) {
			foreach ($optHeaders as $header => $value) {
				$this->setHeader($header, $value);
			}
		}
	}

	public function flatten()
	{

	}
}
