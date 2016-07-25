<?php

namespace Sms;

interface MessageInterface
{
	/**
	 * Get From Field
	 *
	 * @return string
	 */
	public function getMessage();

	/**
	 * Get From Field
	 *
	 * @return string
	 */
	public function getHeader($header);
}
