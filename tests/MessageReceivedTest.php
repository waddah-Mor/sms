<?php

use PHPUnit\Framework\TestCase;
use Sms\MessageReceived;
use Sms\SmsMessageException;
use org\bovigo\vfs\vfsStream;

class MessageReceivedTest extends TestCase
{
	const TEST_FROM_SMSC = '447782000808';

	const TEST_HEADERS = [
		'From'      => 'From Three',
		'From_TOA'  => 'D0 alphanumeric, unknown',
		'Sent'      => '16-07-15 16:58:23',
		'Received'  => '16-07-15 16:02:22',
		'Subject'   => 'GSM1',
		// 'Modem'     => 'GSM1',
		'IMSI'      => '234200003367431',
		'Report'    => 'yes',
		'Alphabet'  => 'ISO',
		'Length'    => '159'
	];

	const TEST_MESSAGE = "Maximise your top-up with an All-in-One Add-on. You could get All-you-can-eat data & use your allowance in Feel at Home destinations. bit.ly/1KCRBck Optout@My3";

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
		$this->file->setContent($this->buildFileContent());
		$this->root->addChild($this->file);
	}

	/**
	 * Build an SMS Message file as though received directly from SMS Server Tools
	 *
	 * @return string
	 */
	public function buildFileContent()
	{
		$str = "From_SMSC: ".self::TEST_FROM_SMSC."\n";
		foreach (self::TEST_HEADERS as $header => $value) {
			$str .= "{$header}: {$value}\n";
		}
		return $str . "\n" . self::TEST_MESSAGE;
	}

	/**
	 * Return array of headers to be used as a data provider
	 *
	 * @return array
	 */
	public function headerIterator()
	{
		$arr = [];
		foreach (self::TEST_HEADERS as $key => $value) {
			$arr[] = [
				'key'   => $key,
				'value' => $value
			];
		}
		return $arr;
	}

	/**
     * @expectedException        Sms\SmsMessageException
     * @expectedExceptionMessage File does not exist.
	 */
	public function testReceivedMessageFileDoesNotExist()
	{
		MessageReceived::createFromPath("vfs://undefined-file.txt");
	}

	/**
     * @expectedException        Sms\SmsMessageException
     * @expectedExceptionMessage File is not readable.
	 */
	public function testReceivedMessageFileIsNotReadable()
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

		return $message;
	}

	/**
	 * @depends testCreateMessageRecievedFromPath
	 */
	public function testMessageReceivedMessagePropertyContent($message)
	{
		$this->assertSame(self::TEST_MESSAGE, $message->getMessage());
	}

	/**
	 * @depends testCreateMessageRecievedFromPath
	 */
	public function testMessageFromSmscPropertyContent($message)
	{
		$this->assertSame(self::TEST_FROM_SMSC, $message->getFromSmsc());
	}

	/**
	 * @depends testCreateMessageRecievedFromPath
	 */
	public function testInvalidMessageHeader($message)
	{
		$this->assertNull($message->getHeader("none-existent-header"));
	}

	/**
	 * @dataProvider headerIterator
	 * @depends testCreateMessageRecievedFromPath
	 */
	public function testGetMessageHeader()
	{
		$args = func_get_args();

		$this->assertSame($args[1], $args[2]->getHeader($args[0]));
	}
}
