<?php

namespace Sms;

abstract class AbstractMessage implements MessageInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function getTo()
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function getFromSmsc()
	{
		return $this->fromSmsc;
	}

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

	protected function validateHeader($header, $content)
	{
		if (!array_key_exists($header, static::HEADER_PARAMETERS)) {
			return false;
		}

		return true;
	}
}
