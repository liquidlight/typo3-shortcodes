<?php

declare(strict_types=1);

namespace LiquidLight\Shortcodes\Tests\Unit;

use LiquidLight\Shortcodes\Keywords\VideoKeyword;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class VideoKeywordTest extends TestCase
{
	private VideoKeyword $keyword;

	protected function setUp(): void
	{
		$this->keyword = new VideoKeyword();
	}

	#[Test]
	public function processShortcodeReplacesIframeClassWithVideo(): void
	{
		$result = $this->keyword->processShortcode(
			'video',
			['value' => 'https://example.com/video'],
			'[video=https://example.com/video]'
		);

		self::assertStringContainsString('class="shortcode video"', $result);
		self::assertStringNotContainsString('class="shortcode iframe"', $result);
	}

	#[Test]
	public function processShortcodeUsesDefaultRatio16x9(): void
	{
		$result = $this->keyword->processShortcode(
			'video',
			['value' => 'https://example.com/video'],
			'[video=https://example.com/video]'
		);

		self::assertStringContainsString('data-ratio="16:9"', $result);
	}

	#[Test]
	public function processShortcodeAllowsCustomRatio(): void
	{
		$result = $this->keyword->processShortcode(
			'video',
			['value' => 'https://example.com/video', 'ratio' => '4:3'],
			'[video=https://example.com/video ratio="4:3"]'
		);

		self::assertStringContainsString('data-ratio="4:3"', $result);
	}

	#[Test]
	public function allowedAttributesIncludesVideoSpecificAttributes(): void
	{
		$reflection = new \ReflectionClass($this->keyword);
		$property = $reflection->getProperty('attributes');

		$attributes = $property->getValue($this->keyword);

		self::assertContains('src', $attributes);
		self::assertContains('code', $attributes);
		self::assertContains('url', $attributes);
		self::assertContains('ratio', $attributes);
		self::assertContains('width', $attributes);
		self::assertContains('height', $attributes);
		self::assertContains('loading', $attributes);
	}
}
