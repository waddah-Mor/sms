<?php

namespace SmsTest;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\visitor\vfsStreamStructureVisitor;
use Sms\Gateway;
use Sms\Message;
use Sms\MessageReceived;

class GatewayTest extends TestCase
{
	private $root;
	private $gateway;
	private $spoolDir;

	private function buildStructure()
	{
		$path = ltrim(
			Gateway::DEFAULT_SPOOL_DIR, '/var'
		);

		$structure = [
			$path.Gateway::CHECKED_DIR  => [],
			$path.Gateway::FAILED_DIR   => [],
			$path.Gateway::INCOMING_DIR => [
				'GSM1.1vW2N4' => 'From: From Three
From_TOA: D0 alphanumeric, unknown
From_SMSC: 447782000808
Sent: 16-07-17 17:13:31
Received: 16-07-17 16:17:26
Subject: GSM1
Modem: GSM1
IMSI: 234200003367431
Report: yes
Alphabet: UCS2
Length: 147

Not having signal indoors sucks. Use Three inTouch to call & text whenever you re on Wi-Fi. #makeitright Get it free here bit.ly/1h4DhzJ Optout@My3'
			],
			$path.Gateway::OUTGOING_DIR => [],
			$path.Gateway::SENT_DIR     => []
		];

		$_structure = [];
		foreach($structure as $path => $value) {
			$temp = &$_structure;
			foreach(explode('/', $path) as $key) {
				$temp =& $temp[$key];
			}
			$temp = $value;
		}

		return $_structure;
	}

	public function setUp()
	{
		$this->root = vfsStream::setup(
			'var',
			null,
			$this->buildStructure()
		);

		/* Useful for testing the structure */
		// $structure = vfsStream::inspect(
		// 	new vfsStreamStructureVisitor()
		// )->getStructure();

		$this->gateway = new Gateway(
			$this->root->getChild('spool/sms')->url()
		);
	}

	/**
     * @expectedException        Sms\SmsGatewayException
     * @expectedExceptionMessage Outgoing folder is not accessible.
	 */
	public function testOutgoingDirectoryDoesNotWritable()
	{
		$this->root->getChild('spool/sms/outgoing')->chmod(0000);

		$message = $this->getMockBuilder("Sms\Message")
			->disableOriginalConstructor()
			->getMock();

		$this->gateway->send($message);
	}

	public function testWriteMessageToOutgoingDirectory()
	{
		$message = $this->getMockBuilder("Sms\Message")
			->disableOriginalConstructor()
			->getMock();

		$message->method('flatten')
			->willReturn("Flattened message...");

		$this->assertFileExists(
			$this->gateway->send($message)
		);
	}

	/**
     * @expectedException        Sms\SmsGatewayException
     * @expectedExceptionMessage Incoming folder is not accessible.
	 */
	public function testOutgoingDirectoryDoesNotReadable()
	{
		$this->root->getChild('spool/sms/incoming')->chmod(0000);

		$message = $this->getMockBuilder(Message::class)
			->disableOriginalConstructor()
			->getMock();

		$this->gateway->getIncoming();
	}

	public function testCanReadIncomingMessages()
	{
		$messages = $this->gateway->getIncoming();

		$this->assertContainsOnlyInstancesOf(
			MessageReceived::class,
			$messages
		);
	}

	public function testGetIncomingMessagesCanAlsoExpungeExistingFiles()
	{
		$messages = $this->gateway->getIncoming(true);

		$this->assertEmpty(
			$this->root->getChild('spool/sms/incoming')->getChildren()
		);
	}
}
