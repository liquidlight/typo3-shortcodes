<?php

declare(strict_types=1);

namespace LiquidLight\Shortcodes\Tests\Unit;

use LiquidLight\Shortcodes\Keywords\IframeKeyword;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class IframeKeywordTest extends TestCase
{
	private IframeKeyword $keyword;

	protected function setUp(): void
	{
		$this->keyword = new IframeKeyword();
	}

	#[Test]
	public function processShortcodeCreatesIframeDivWithValue(): void
	{
		$result = $this->keyword->processShortcode(
			'iframe',
			['value' => 'https://example.com'],
			'[iframe=https://example.com]'
		);

		self::assertStringContainsString('<div class="shortcode iframe"', $result);
		self::assertStringContainsString('<iframe src="https://example.com"', $result);
		self::assertStringContainsString('</iframe></div>', $result);
	}

	#[Test]
	public function processShortcodeUsesSrcAttributeWhenNoValue(): void
	{
		$result = $this->keyword->processShortcode(
			'iframe',
			['src' => 'https://example.com/embed'],
			'[iframe src="https://example.com/embed"]'
		);

		self::assertStringContainsString('src="https://example.com/embed"', $result);
	}

	#[Test]
	public function processShortcodeAddsRatioDataAttribute(): void
	{
		$result = $this->keyword->processShortcode(
			'iframe',
			['value' => 'https://example.com', 'ratio' => '16:9'],
			'[iframe=https://example.com ratio="16:9"]'
		);

		self::assertStringContainsString('data-ratio="16:9"', $result);
	}

	#[Test]
	public function processShortcodeCalculatesRatioFromWidthAndHeight(): void
	{
		$result = $this->keyword->processShortcode(
			'iframe',
			['value' => 'https://example.com', 'width' => '1600', 'height' => '900'],
			'[iframe=https://example.com width="1600" height="900"]'
		);

		self::assertStringContainsString('data-ratio="16:9"', $result);
	}

	#[Test]
	public function processShortcodeHandlesCustomAttributes(): void
	{
		$result = $this->keyword->processShortcode(
			'iframe',
			['value' => 'https://example.com', 'allowfullscreen' => 'true', 'frameBorder' => '0'],
			'[iframe=https://example.com allowfullscreen="true" frameBorder="0"]'
		);

		self::assertStringContainsString('allowfullscreen="true"', $result);
		self::assertStringContainsString('frameBorder="0"', $result);
	}

	#[Test]
	public function getRatioConvertsSlashToColon(): void
	{
		$reflection = new \ReflectionClass($this->keyword);
		$method = $reflection->getMethod('getRatio');

		$result = $method->invoke($this->keyword, ['ratio' => '16/9']);

		self::assertSame('16:9', $result);
	}

	#[Test]
	public function getRatioReturnsDefaultWhenNoWidthOrHeight(): void
	{
		$reflection = new \ReflectionClass($this->keyword);
		$method = $reflection->getMethod('getRatio');

		$result = $method->invoke($this->keyword, [], '4:3');

		self::assertSame('4:3', $result);
	}

	#[Test]
	public function getRatioCalculatesFromWidthAndHeight(): void
	{
		$reflection = new \ReflectionClass($this->keyword);
		$method = $reflection->getMethod('getRatio');

		$result = $method->invoke($this->keyword, ['width' => '800', 'height' => '600']);

		self::assertSame('4:3', $result);
	}

	#[Test]
	public function getRatioStripsNonNumericCharacters(): void
	{
		$reflection = new \ReflectionClass($this->keyword);
		$method = $reflection->getMethod('getRatio');

		$result = $method->invoke($this->keyword, ['width' => '1920px', 'height' => '1080px']);

		self::assertSame('16:9', $result);
	}

	#[Test]
	public function getRatioHandlesSimplifiedRatios(): void
	{
		$reflection = new \ReflectionClass($this->keyword);
		$method = $reflection->getMethod('getRatio');

		$result = $method->invoke($this->keyword, ['width' => '1280', 'height' => '720']);

		self::assertSame('16:9', $result);
	}

	#[Test]
	public function processShortcodeHandlesEmptyValue(): void
	{
		$result = $this->keyword->processShortcode(
			'iframe',
			[],
			'[iframe]'
		);

		self::assertStringContainsString('src=""', $result);
	}
}
