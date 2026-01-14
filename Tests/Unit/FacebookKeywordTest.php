<?php

declare(strict_types=1);

namespace LiquidLight\Shortcodes\Tests\Unit;

use LiquidLight\Shortcodes\Keywords\FacebookKeyword;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FacebookKeywordTest extends TestCase
{
	private FacebookKeyword $keyword;

	protected function setUp(): void
	{
		$this->keyword = new FacebookKeyword();
	}

	#[Test]
	public function processShortcodeCreatesVideoEmbedForVideoUrls(): void
	{
		$result = $this->keyword->processShortcode(
			'facebook',
			['value' => 'https://www.facebook.com/videos/123456789'],
			'[facebook=https://www.facebook.com/videos/123456789]'
		);

		self::assertStringContainsString('<div class="shortcode video facebook"', $result);
		self::assertStringContainsString('<iframe src="https://www.facebook.com/plugins/video.php?href=', $result);
		self::assertStringContainsString('allowfullscreen', $result);
	}

	#[Test]
	public function processShortcodeCreatesPostEmbedForNonVideoUrls(): void
	{
		$result = $this->keyword->processShortcode(
			'facebook',
			['value' => 'https://www.facebook.com/user/posts/123456789'],
			'[facebook=https://www.facebook.com/user/posts/123456789]'
		);

		self::assertStringContainsString('fb-post', $result);
		self::assertStringContainsString('data-href=', $result);
		self::assertStringContainsString('sdk.js', $result);
	}

	#[Test]
	public function processShortcodeUsesKeywordFacebookvideo(): void
	{
		$result = $this->keyword->processShortcode(
			'facebookvideo',
			['value' => 'https://www.facebook.com/user/posts/123456789'],
			'[facebookvideo=https://www.facebook.com/user/posts/123456789]'
		);

		self::assertStringContainsString('video.php', $result);
	}

	#[Test]
	public function processShortcodePostIncludesDefaultWidth(): void
	{
		$result = $this->keyword->processShortcode(
			'facebook',
			['value' => 'https://www.facebook.com/post/123'],
			'[facebook=https://www.facebook.com/post/123]'
		);

		self::assertStringContainsString('width="500"', $result);
	}

	#[Test]
	public function processShortcodePostAllowsCustomWidth(): void
	{
		$result = $this->keyword->processShortcode(
			'facebook',
			['value' => 'https://www.facebook.com/post/123', 'width' => '600'],
			'[facebook=https://www.facebook.com/post/123 width="600"]'
		);

		self::assertStringContainsString('width="600"', $result);
		self::assertStringNotContainsString('width="500"', $result);
	}

	#[Test]
	public function processShortcodePostIncludesCustomHeight(): void
	{
		$result = $this->keyword->processShortcode(
			'facebook',
			['value' => 'https://www.facebook.com/post/123', 'height' => '400'],
			'[facebook=https://www.facebook.com/post/123 height="400"]'
		);

		self::assertStringContainsString('height="400"', $result);
	}

	#[Test]
	public function processShortcodeVideoIncludesTitle(): void
	{
		$result = $this->keyword->processShortcode(
			'facebook',
			['value' => 'https://www.facebook.com/videos/123', 'title' => 'My Video'],
			'[facebook=https://www.facebook.com/videos/123 title="My Video"]'
		);

		self::assertStringContainsString('title="My Video"', $result);
	}

	#[Test]
	public function allowedAttributesIncludesHeightAndWidth(): void
	{
		$reflection = new \ReflectionClass($this->keyword);
		$property = $reflection->getProperty('attributes');

		$attributes = $property->getValue($this->keyword);

		self::assertContains('height', $attributes);
		self::assertContains('width', $attributes);
	}
}
