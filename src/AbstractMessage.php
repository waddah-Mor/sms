<?php

namespace Sms;

abstract class AbstractMessage implements MessageInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function getMessage()
	{
		return $this->message;
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
		if (!($properties = static::HEADER_PARAMETERS[$header])) {
			return false;
		}

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
