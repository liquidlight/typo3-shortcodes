<?php

declare(strict_types=1);

namespace LiquidLight\Shortcodes\Tests\Unit;

use LiquidLight\Shortcodes\Keywords\VimeoKeyword;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class VimeoKeywordTest extends TestCase
{
	private VimeoKeyword $keyword;

	protected function setUp(): void
	{
		$this->keyword = new VimeoKeyword();
	}

	#[Test]
	public function processShortcodeCreatesVimeoEmbed(): void
	{
		$result = $this->keyword->processShortcode(
			'vimeo',
			['value' => '123456789'],
			'[vimeo=123456789]'
		);

		self::assertStringContainsString('<div class="shortcode video vimeo"', $result);
		self::assertStringContainsString('https://player.vimeo.com/video/123456789', $result);
		self::assertStringContainsString('allowfullscreen', $result);
	}

	#[Test]
	public function processShortcodeUsesCodeAttribute(): void
	{
		$result = $this->keyword->processShortcode(
			'vimeo',
			['code' => '987654321'],
			'[vimeo code="987654321"]'
		);

		self::assertStringContainsString('https://player.vimeo.com/video/987654321', $result);
	}

	#[Test]
	#[DataProvider('vimeoUrlProvider')]
	public function processShortcodeExtractsCodeFromVimeoUrls(string $url, string $expectedPath): void
	{
		$result = $this->keyword->processShortcode(
			'vimeo',
			['value' => $url],
			"[vimeo={$url}]"
		);

		self::assertStringContainsString("https://player.vimeo.com/video/{$expectedPath}", $result);
	}

	public static function vimeoUrlProvider(): array
	{
		return [
			'standard vimeo url' => ['https://vimeo.com/123456789', '123456789'],
			'unlisted video with hash' => ['https://vimeo.com/123456789/abc123def', '123456789?h=abc123def'],
			'channel video' => ['https://vimeo.com/channels/staffpicks/123456789', '123456789'],
		];
	}

	#[Test]
	public function processShortcodeHandlesUnlistedVideos(): void
	{
		$result = $this->keyword->processShortcode(
			'vimeo',
			['value' => 'https://vimeo.com/123456789/abc123'],
			'[vimeo=https://vimeo.com/123456789/abc123]'
		);

		self::assertStringContainsString('123456789?h=abc123', $result);
	}

	#[Test]
	public function processShortcodeIncludesDefaultRatio16x9(): void
	{
		$result = $this->keyword->processShortcode(
			'vimeo',
			['value' => '123456789'],
			'[vimeo=123456789]'
		);

		self::assertStringContainsString('data-ratio="16:9"', $result);
	}

	#[Test]
	public function processShortcodeIncludesTitle(): void
	{
		$result = $this->keyword->processShortcode(
			'vimeo',
			['value' => '123456789', 'title' => 'My Vimeo Video'],
			'[vimeo=123456789 title="My Vimeo Video"]'
		);

		self::assertStringContainsString('title="My Vimeo Video"', $result);
	}

	#[Test]
	public function processShortcodeIncludesLazyLoadingByDefault(): void
	{
		$result = $this->keyword->processShortcode(
			'vimeo',
			['value' => '123456789'],
			'[vimeo=123456789]'
		);

		self::assertStringContainsString('loading="lazy"', $result);
	}

	#[Test]
	public function processShortcodeIncludesWidthAndHeight(): void
	{
		$result = $this->keyword->processShortcode(
			'vimeo',
			['value' => '123456789', 'width' => '800', 'height' => '450'],
			'[vimeo=123456789 width="800" height="450"]'
		);

		self::assertStringContainsString('width="800"', $result);
		self::assertStringContainsString('height="450"', $result);
	}

	#[Test]
	public function processShortcodeHandlesPlainCode(): void
	{
		$result = $this->keyword->processShortcode(
			'vimeo',
			['value' => '987654321'],
			'[vimeo=987654321]'
		);

		self::assertStringContainsString('/video/987654321', $result);
	}

	#[Test]
	public function processShortcodeIncludesDntParameterByDefault(): void
	{
		$result = $this->keyword->processShortcode(
			'vimeo',
			['value' => '123456789'],
			'[vimeo=123456789]'
		);

		self::assertStringContainsString('?dnt=1', $result);
	}

	#[Test]
	public function processShortcodeAppendsDntToUnlistedVideoQueryString(): void
	{
		$result = $this->keyword->processShortcode(
			'vimeo',
			['value' => 'https://vimeo.com/123456789/abc123'],
			'[vimeo=https://vimeo.com/123456789/abc123]'
		);

		// Unlisted videos have ?h=hash, so dnt should be appended with &
		self::assertStringContainsString('?h=abc123&dnt=1', $result);
	}

	#[Test]
	public function processShortcodeDoesNotDuplicateDntParameter(): void
	{
		$result = $this->keyword->processShortcode(
			'vimeo',
			['value' => '123456789'],
			'[vimeo=123456789]'
		);

		// Count occurrences of 'dnt=' in the result
		$count = substr_count($result, 'dnt=');

		self::assertSame(1, $count, 'DNT parameter should appear exactly once');
	}
}
