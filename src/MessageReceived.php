<?php
/**
 * Copyright (c) 2016 Liam Jones
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 */

namespace Sms;

/**
 * Message received object
 *
 * @author Liam Jones <liam@plumbnation.co.uk>
 * @since  1.0.0
 */
class MessageReceived extends AbstractMessage implements MessageInterface
{
	/**
	 * Filepath
	 *
	 * @var string
	 */
	private $filepath;

	/**
	 * Message
	 *
	 * @var string
	 */
	private $message;

	/**
	 * From
	 *
	 * @var string  Telephone number
	 */
	private $from;

	/**
	 * SMS Headers
	 *
	 * @var stdClass
	 */
	private $headers;

	/**
	 * Class constructor
	 *
	 * @param string $filepath
	 * @param string $message
	 * @param string $from
	 * @param array  $optHeaders
	 *
	 * @return Sms\MessageRecieved
	 */
	private function __construct(
		$filepath,
		$message,
		$from,
		array $optHeaders = null
	) {
		$this->setMessage($message);
		$this->setFrom($message);

		if (!is_null($optHeaders)) {
			foreach ($optHeaders as $header => $content) {
				$this->setHeader($header, $content);
			}
		}
	}

	/**
	 * Set message
	 *
	 * @param string $message
	 */
	private function setMessage($message)
	{
		$this->message = $message;
	}

	/**
	 * Set from
	 *
	 * @param string $from
	 */
	private function setFrom($from)
	{
		$this->from = $from;
	}

	/**
	 * Set headers
	 *
	 * @param string $header
	 * @param string $content
	 */
	private function setHeader($header, $content)
	{
		if (is_null($this->headers)) {
			$this->headers = new \stdClass;
		}

		if (!$this->validateHeader($header, $content)) {
			return false;
		}

		$this->headers->{$header} = $content;
	}

	/**
	 * Iterate line-by-line over a file pointer and greedily parse what we can
	 *
	 * @param  resource $pointer
	 *
	 * @return array             [(string) message, (string) from, (array) headers]
	 */
	private static function parseFile($path)
	{
		if (($pointer = @fopen($path, 'r')) === false) {
			throw new SmsMessageException(
				"File is not readable."
			);
		}

		$from    = "";
		$headers = [];

		while (($line = trim(fgets($pointer))) !== false) {
			if (empty($line)) {
				break;
			}

			$line = explode(': ', trim($line));

			switch ($line[0]) {
				case "From_SMSC":
					$from = $line[1];
					break;
				default:
					$headers[$line[0]] = $line[1];
			}
		}

		$message = fgets($pointer);

		return [
			$path, $message, $from, $headers
		];
	}

	/**
	 * Create new instance of self from filepath
	 *
	 * @param  string $pointer
	 *
	 * @return Sms\MessageReceived
	 *
	 * @throws Sms\SmsMessageException
	 */
	public static function createFromPath($path)
	{
		if (!file_exists($path)) {
			throw new SmsMessageException(
				"File does not exist."
			);
		}

		return new self(
			...self::parseFile($path)
		);
	}
}
