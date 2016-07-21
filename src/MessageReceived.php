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
 * Message received value object
 *
 * @author Liam Jones <liam@plumbnation.co.uk>
 * @since  1.0.0
 */
class MessageReceived extends AbstractMessage implements MessageInterface
{
	/**
	 * Class constructor
	 *
	 * @param string $message
	 * @param string $from
	 * @param array  $optHeaders
	 *
	 * @return Sms\MessageRecieved
	 */
	private function __construct($message, $from, array $optHeaders = null)
	{
		$this->setMessage($message);
		$this->setTo((array) $message);

		if (!is_null($optHeaders)) {
			foreach ($optHeaders as $header => $value) {
				$this->setHeader($header, $value);
			}
		}
	}

	/**
	 * Iterate line-by-line over a file pointer and greedily parse what we can
	 *
	 * @param  resource $pointer
	 *
	 * @return array             [(string) message, (string) from, (array) headers]
	 */
	private function parseSmsFromPointer($pointer)
	{
		return [
			$message, $from, $headers
		];
	}

	/**
	 * Create new instance of self from file pointer
	 *
	 * @param  resource $pointer
	 *
	 * @return Sms\MessageReceived
	 *
	 * @throws Sms\SmsMessageException
	 */
	public static function createFromFilePointer($pointer)
	{
		if (!is_resource($pointer)) {
			throw new SmsMessageException(
				"From pointer expects a valid file handler returned from the likes of fopen."
			);
		}

		rewind($pointer);

		return new self(
			...$this->parseSmsFromPointer($pointer)
		);
	}
}
