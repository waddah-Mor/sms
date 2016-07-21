<?php

namespace Sms;

abstract class AbstractMessage implements MessageInterface
{
	const HEADER_PARAMETERS = [];

	/**
	 * {@inheritDoc}
	 */
	public function getTo()
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function getFrom()
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function getMessage()
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function getHeader($header)
	{

	}
}
