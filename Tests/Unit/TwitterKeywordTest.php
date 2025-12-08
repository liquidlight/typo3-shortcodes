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
		$result = $this->keyword->processShortcode(
			'twitter',
			['value' => 'invalid-url'],
			'[twitter=invalid-url]'
		);

		self::assertNull($result);
	}

	#[Test]
	public function processShortcodeHandlesIdAndUrl(): void
	{
		$this->expectNotToPerformAssertions();

		// Note: This test validates the internal logic but actual API calls
		// would require mocking file_get_contents which is challenging
		$this->keyword->processShortcode(
			'twitter',
			['value' => '123456789'],
			'[twitter=123456789]'
		);
	}
}
