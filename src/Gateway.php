<?php

namespace Sms;

class Gateway
{
	const DEFAULT_SPOOL_DIR = "/var/spool/sms";
	const CHECKED_DIR = "/checked";
	const FAILED_DIR = "/failed";
	const INCOMING_DIR = "/incoming";
	const OUTGOING_DIR = "/outgoing";
	const SENT_DIR = "/sent";

	/**
	 * Class constructor
	 *
	 * @param string|null $spoolDir
	 * @param array       $optParams
	 *
	 * @return  Sms\Gateway
	 *
	 * @throws  Sms\SmsGatewayException
	 */
	public function __construct(
		$spoolDir = self::DEFAULT_SPOOL_DIR,
		array $optParams = []
	) {
	}

	/**
	 * Send SMS
	 *
	 * @param  MessageInterface $message
	 */
	public function send(Message $message)
	{
	}
}
