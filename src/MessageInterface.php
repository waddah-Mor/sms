<?php

namespace Sms;

interface MessageInterface
{
	/**
	 * Get To Field
	 *
	 * @return string
	 */
	public function getTo();

	/**
	 * Get From SMSC Field
	 *
	 * @return string
	 */
	public function getFromSmsc();

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
