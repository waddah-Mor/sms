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
	 * Get From Field
	 *
	 * @return string
	 */
	public function getFrom();

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
