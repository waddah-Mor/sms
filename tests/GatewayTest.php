<?php

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use Sms\Gateway;
use Sms\MessageReceived;

class GatewayTest extends TestCase
{
	private $root;

	public function setUp()
	{
		$structure = [
			"checked"  => [],
			"failed"   => [],
			"incoming" => [],
			"outgoing" => [],
			"sent"     => []
		];

		$this->root = vfsStream::setup(
			"/var/spool/sms",
			null,
			$structure
		);
	}

	public function testSpoolDirectoryDoesNotExist()
	{
	}
}
