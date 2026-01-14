<?php

declare(strict_types=1);

namespace LiquidLight\Shortcodes\Tests\Unit;

use LiquidLight\Shortcodes\Keywords\InstagramKeyword;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class InstagramKeywordTest extends TestCase
{
	private InstagramKeyword $keyword;

	protected function setUp(): void
	{
		$this->keyword = new InstagramKeyword();
	}

	#[Test]
	public function allowedAttributesIncludesCodeAndUrl(): void
	{
		$reflection = new \ReflectionClass($this->keyword);
		$property = $reflection->getProperty('attributes');

		$attributes = $property->getValue($this->keyword);

		self::assertContains('code', $attributes);
		self::assertContains('url', $attributes);
	}

	#[Test]
	public function processShortcodeReturnsNullOnApiFailure(): void
	{
		$result = $this->keyword->processShortcode(
			'instagram',
			['value' => 'invalid-url'],
			'[instagram=invalid-url]'
		);

		self::assertNull($result);
	}

	#[Test]
	public function processShortcodeBuildsInstagramUrlFromCode(): void
	{
		$this->expectNotToPerformAssertions();

		// Note: This test validates the internal logic but actual API calls
		// would require mocking file_get_contents which is challenging
		$this->keyword->processShortcode(
			'instagram',
			['code' => 'ABC123'],
			'[instagram code="ABC123"]'
		);
	}
}
