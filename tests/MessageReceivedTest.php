<?php

use PHPUnit\Framework\TestCase;
use Sms\MessageReceived;

class MessageReceivedTest extends TestCase
{
	public function testMessageRecievedFromPointer()
	{
		$message = MessageReceived::createFromFilePointer();

		$this->assertInstanceOf(
			MessageReceived::class,
			$message
		);
	}
}
