<?php

namespace Sms;

abstract class AbstractMessage implements MessageInterface
{
	/**
	 * Set Body
	 *
	 * @param string $body
	 *
	 * @throws  Sms\SmsMessageException
	 */
	protected function setBody($body = "")
	{
		$this->body = $body;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getBody()
	{
		return $this->body;
	}

	/**
	 * Set headers
	 *
	 * @param string $header
	 * @param string $content
	 */
	protected function setHeader($header, $content)
	{
		if (is_null($this->headers)) {
			$this->headers = new \stdClass;
		}

		if (!$this->validateHeader($header, $content)) {
			return false;
		}

		$this->headers->{$header} = $content;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getHeader($header)
	{
		if (
			property_exists($this->headers, $header)
			&& array_key_exists($header, static::HEADER_PARAMETERS)
		) {
			return $this->headers->{$header};
		}
	}

	/**
	 * Validate SMS Header
	 *
	 * @param  string $header
	 * @param  string $content
	 *
	 * @return boolean
	 */
	protected function validateHeader($header, $content)
	{
		if (!array_key_exists($header, static::HEADER_PARAMETERS)) {
			return false;
		}

		$properties = static::HEADER_PARAMETERS[$header];

		switch ($properties['type']) {
			case 'stringsmall':
				return is_string($properties['type'])
					&& ($strlen = strlen($content)) > 0
					&& $strlen < 255;
			default:
				return true;
		}
	}
}
