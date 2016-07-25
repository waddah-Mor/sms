<?php

namespace Sms;

interface MessageInterface
{
	/**
	 * Get Body
	 *
	 * @return string
	 */
	public function getBody();

	/**
	 * Get From Field
	 *
	 * @return string
	 */
	public function getHeader($header);
}
