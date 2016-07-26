<?php

namespace SmsTest;

use PHPUnit\Framework\TestCase;
use Sms\Message;

class MessageTest extends TestCase
{
	const TEST_BODY = "Test message body";
	const TEST_TO = "447734541961";
	const TEST_HEADERS = [
		'To_TOA'         => '',
		'Flash'          => '',
		'Alphabet'       => '',
		'UDH'            => '',
		'UDH-DATA'       => '',
		'SMSC'           => '',
		'Provider Queue' => '',
		'Report'         => '',
		'Autosplit'      => '',
		'Priority'       => '',
		'Validity'       => '',
		'Voicecall'      => '',
		'Hex'            => '',
		'Replace'        => '',
		'Include'        => '',
		'Macro'          => '',
		'System_message' => ''
	];

	public function testInstantiation()
	{
		$message = new Message(
			self::TEST_BODY,
			self::TEST_TO,
			self::TEST_HEADERS
		);

		$this->assertInstanceOf(
			Message::class,
			$message
		);

		return $message;
	}

	/**
	 * @depends testInstantiation
	 */
	public function testFlatten($message)
	{
		$str = "To: ".self::TEST_TO."\n";
		foreach (self::TEST_HEADERS as $header => $content) {
			$str .= "{$header}: {$content}\n";
		}
		$str .= "\n".self::TEST_BODY;

		$this->assertSame($str, $message->flatten());
	}
}
