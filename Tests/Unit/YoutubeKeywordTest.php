<?php

declare(strict_types=1);

namespace LiquidLight\Shortcodes\Tests\Unit;

use LiquidLight\Shortcodes\Keywords\YoutubeKeyword;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class YoutubeKeywordTest extends TestCase
{
	private YoutubeKeyword $keyword;

	protected function setUp(): void
	{
		$this->keyword = new YoutubeKeyword();
	}

	#[Test]
	public function processShortcodeCreatesYoutubeEmbed(): void
	{
		$result = $this->keyword->processShortcode(
			'youtube',
			['value' => 'abc123'],
			'[youtube=abc123]'
		);

		self::assertStringContainsString('<div class="shortcode video youtube"', $result);
		self::assertStringContainsString('https://www.youtube-nocookie.com/embed/abc123', $result);
		self::assertStringContainsString('allowfullscreen', $result);
	}

	#[Test]
	public function processShortcodeUsesCodeAttribute(): void
	{
		$result = $this->keyword->processShortcode(
			'youtube',
			['code' => 'xyz789'],
			'[youtube code="xyz789"]'
		);

		self::assertStringContainsString('https://www.youtube-nocookie.com/embed/xyz789', $result);
	}

	#[Test]
	public function processShortcodeUsesUrlAttribute(): void
	{
		$result = $this->keyword->processShortcode(
			'youtube',
			['url' => 'def456'],
			'[youtube url="def456"]'
		);

		self::assertStringContainsString('https://www.youtube-nocookie.com/embed/def456', $result);
	}

	#[Test]
	public function processShortcodePrioritizesCodeOverUrl(): void
	{
		$result = $this->keyword->processShortcode(
			'youtube',
			['code' => 'code123', 'url' => 'url456'],
			'[youtube code="code123" url="url456"]'
		);

		self::assertStringContainsString('/embed/code123', $result);
		self::assertStringNotContainsString('/embed/url456', $result);
	}

	#[Test]
	#[DataProvider('youtubeUrlProvider')]
	public function processShortcodeExtractsCodeFromYoutubeUrls(string $url, string $expectedCode): void
	{
		$result = $this->keyword->processShortcode(
			'youtube',
			['value' => $url],
			"[youtube={$url}]"
		);

		self::assertStringContainsString("/embed/{$expectedCode}", $result);
	}

	public static function youtubeUrlProvider(): array
	{
		return [
			'watch url' => ['https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
			'youtu.be short url' => ['https://youtu.be/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
			'embed url' => ['https://www.youtube.com/embed/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
			'watch url with params' => ['https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=10s', 'dQw4w9WgXcQ'],
			'without https' => ['youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
		];
	}

	#[Test]
	public function processShortcodeIncludesDefaultRatio16x9(): void
	{
		$result = $this->keyword->processShortcode(
			'youtube',
			['value' => 'abc123'],
			'[youtube=abc123]'
		);

		self::assertStringContainsString('data-ratio="16:9"', $result);
	}

	#[Test]
	public function processShortcodeIncludesTitle(): void
	{
		$result = $this->keyword->processShortcode(
			'youtube',
			['value' => 'abc123', 'title' => 'My Video'],
			'[youtube=abc123 title="My Video"]'
		);

		self::assertStringContainsString('title="My Video"', $result);
	}

	#[Test]
	public function processShortcodeIncludesLazyLoadingByDefault(): void
	{
		$result = $this->keyword->processShortcode(
			'youtube',
			['value' => 'abc123'],
			'[youtube=abc123]'
		);

		self::assertStringContainsString('loading="lazy"', $result);
	}

	#[Test]
	public function processShortcodeAllowsCustomLoading(): void
	{
		$result = $this->keyword->processShortcode(
			'youtube',
			['value' => 'abc123', 'loading' => 'eager'],
			'[youtube=abc123 loading="eager"]'
		);

		self::assertStringContainsString('loading="eager"', $result);
		self::assertStringNotContainsString('loading="lazy"', $result);
	}

	#[Test]
	public function processShortcodeIncludesWidthAndHeight(): void
	{
		$result = $this->keyword->processShortcode(
			'youtube',
			['value' => 'abc123', 'width' => '640', 'height' => '360'],
			'[youtube=abc123 width="640" height="360"]'
		);

		self::assertStringContainsString('width="640"', $result);
		self::assertStringContainsString('height="360"', $result);
	}

	#[Test]
	public function processShortcodeHandlesPlainCode(): void
	{
		$result = $this->keyword->processShortcode(
			'youtube',
			['value' => 'abc123xyz'],
			'[youtube=abc123xyz]'
		);

		self::assertStringContainsString('/embed/abc123xyz', $result);
	}
}
