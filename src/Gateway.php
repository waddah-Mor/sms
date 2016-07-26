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

	const SERVICE_COMMAND = "service smstools status";

	/**
	 * Spool Dir
	 *
	 * @var string
	 */
	private $spoolDir;

	/**
	 * File Prefix
	 *
	 * @var string
	 */
	private $filePrefix;

	/**
	 * Class constructor
	 *
	 * @param  string|null $spoolDir
	 * @param  array       $optParams
	 *
	 * @return  Sms\Gateway
	 *
	 * @throws  Sms\SmsGatewayException
	 */
	public function __construct(
		$spoolDir = self::DEFAULT_SPOOL_DIR,
		$filePrefix = "",
		array $optParams = []
	) {
		$this->spoolDir   = $spoolDir;
		$this->filePrefix = $filePrefix;
	}

	/**
	 * Send SMS
	 *
	 * @param  Sms\Message $message
	 */
	public function send(Message $message)
	{
		$outgoingDir = $this->spoolDir . self::OUTGOING_DIR;
		if (
			!file_exists($outgoingDir)
			|| !is_writable($outgoingDir)
		) {
			throw new SmsGatewayException(
				"Outgoing folder is not accessible."
			);
		}

		$file = tempnam($outgoingDir, $this->filePrefix);
		$fp   = fopen($file, 'w');

		fwrite(
			$fp,
			$message->flatten()
		);

		fclose($fp);

		return $file;
	}

	/**
	 * Get all incoming messages
	 *
	 * @param  boolean $expunge Do you wish to delete the messages once read?
	 *
	 * @return array
	 */
	public function getIncoming($expunge = false)
	{
		$incomingDir = $this->spoolDir . self::INCOMING_DIR;

		if (
			!file_exists($incomingDir)
			|| !is_writable($incomingDir)
		) {
			throw new SmsGatewayException(
				"Incoming folder is not accessible."
			);
		}

		$iterator = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($incomingDir . "/"),
			\RecursiveIteratorIterator::CHILD_FIRST
		);

		$messages = [];
		foreach ($iterator as $file) {
			if (preg_match('/GSM1\.[a-z0-9]/', $file->getFilename())) {
				$messages[] = MessageReceived::createFromPath($file->getPathname());

				if ($expunge) {
					unlink($file->getPathname());
				}
			}
		}

		return $messages;
	}

	/**
	 * Test to see if the sms service is running
	 *
	 * @return  true
	 *
	 * @throws  Sms\SmsGatewayException
	 */
	private function smsServiceIsRunning()
	{
		/* Remove once I work out system func mocking */
		return true;

		$status = shell_exec(self::SERVICE_COMMAND);

		exit;
	}
}
