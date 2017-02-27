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

		chmod($file, 0777);

		return $file;
	}

	/**
	 * Get failed message
	 *
	 * @param  boolean $fail Do you wish to delete the messages once read?
	 *
	 * @return array
	 */
	public function getFailed($fail = false)
	{

		$failDir = $this->spoolDir . self::FAILED_DIR;

		if (
			!file_exists($failDir)
			|| !is_readable($failDir)
		) {
			throw new SmsGatewayException(
				"Failed folder is not accessible."
			);
		}

		//getting path of last modified failed log file
		$failDir .= '/';

		$lastMod = 0;

		foreach (scandir($failDir) as $entry) {
		    if (is_file($failDir.$entry) && filectime($failDir.$entry) > $lastMod) {
		        $lastMod = filectime($failDir.$entry);
		    }
		}

		$path = $failDir.$entry; //full path to the last modified log file

		$failedLog[] = MessageFailed::createFromPath($path);

		if ($fail) {
			unlink($path);
			if (file_exists($path)) {
				throw new SmsGatewayException(
					"Unable to unlink failed log: '{$path}'"
				);
			}
		}

		return $failedLog;
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
			|| !is_readable($incomingDir)
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
			if (preg_match('/GSM1\.[a-z0-9]/i', $file->getFilename())) {
				$path = $file->getPathname();
				$messages[] = MessageReceived::createFromPath($path);

				if ($expunge) {
					unlink($path);
					if (file_exists($path)) {
						throw new SmsGatewayException(
							"Unable to unlink sms: '{$path}'"
						);
					}
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
