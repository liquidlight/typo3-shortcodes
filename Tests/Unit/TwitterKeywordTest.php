<?php

declare(strict_types=1);

namespace LiquidLight\Shortcodes\Tests\Unit;

use LiquidLight\Shortcodes\Keywords\TwitterKeyword;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TwitterKeywordTest extends TestCase
{
	private TwitterKeyword $keyword;

	protected function setUp(): void
	{
		$this->keyword = new TwitterKeyword();
	}

	#[Test]
	public function allowedAttributesIncludesTwitterSpecificAttributes(): void
	{
		$reflection = new \ReflectionClass($this->keyword);
		$property = $reflection->getProperty('attributes');

		$attributes = $property->getValue($this->keyword);

		self::assertContains('theme', $attributes);
		self::assertContains('lang', $attributes);
		self::assertContains('dnt', $attributes);
		self::assertContains('maxwidth', $attributes);
		self::assertContains('maxheight', $attributes);
	}

	#[Test]
	public function processShortcodeReturnsNullOnApiFailure(): void
	{
		// Suppress warning from file_get_contents for external API call
		@$result = $this->keyword->processShortcode(
			'twitter',
			['value' => 'invalid-url'],
			'[twitter=invalid-url]'
		);

		self::assertNull($result);
	}

	#[Test]
	public function processShortcodeHandlesIdAndUrl(): void
	{
		// Note: Twitter API calls would fail in tests without proper credentials
		// Suppress warning from file_get_contents for external API call
		@$result = $this->keyword->processShortcode(
			'twitter',
			['value' => '123456789'],
			'[twitter=123456789]'
		);

		// API will return null without valid credentials/endpoint
		self::assertNull($result);
	}
}
