<?php

use PHPUnit\Framework\TestCase;
use Sms\MessageReceived;
use Sms\SmsMessageException;
use org\bovigo\vfs\vfsStream;

class MessageReceivedTest extends TestCase
{
	const TEST_MESSAGE = "From: From Three
From_TOA: D0 alphanumeric, unknown
From_SMSC: 447782000808
Sent: 16-07-15 16:58:23
Received: 16-07-15 16:02:22
Subject: GSM1
Modem: GSM1
IMSI: 234200003367431
Report: yes
Alphabet: ISO
Length: 159

Maximise your top-up with an All-in-One Add-on. You could get All-you-can-eat data & use your allowance in Feel at Home destinations. bit.ly/1KCRBck Optout@My3";

	/**
	 * instance to test
	 *
	 * @var org\bovigo\vfs\vfsStreamDirectory
	 */
	protected $root;

	/**
	 * instance to test
	 *
	 * @var org\bovigo\vfs\vfsStreamFile
	 */
	protected $file;

	/**
	 * set up test environment
	 */
	public function setUp()
	{
		$this->root = vfsStream::setup();
		$this->file = vfsStream::newFile('received-message-test.txt');
		$this->file->setContent(self::TEST_MESSAGE);
		$this->root->addChild($this->file);
	}

	public function tearDown()
	{
	}

	/**
     * @expectedException        Sms\SmsMessageException
     * @expectedExceptionMessage File does not exist.
	 */
	public function testReceivedMessageFileDoesNotExist()
	{
		MessageReceived::createFromPath("vfs://received-message-test.txt");
	}

	/**
     * @expectedException        Sms\SmsMessageException
     * @expectedExceptionMessage File is not readable.
	 */
	public function testReceivedMessageIsNotReadable()
	{
		$this->file->chmod(0000);

		MessageReceived::createFromPath($this->file->url());
	}

	public function testCreateMessageRecievedFromPath()
	{
		$message = MessageReceived::createFromPath($this->file->url());

		$this->assertInstanceOf(
			MessageReceived::class,
			$message
		);
	}
}
