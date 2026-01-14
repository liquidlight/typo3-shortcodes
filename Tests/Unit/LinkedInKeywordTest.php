<?php

declare(strict_types=1);

namespace LiquidLight\Shortcodes\Tests\Unit;

use LiquidLight\Shortcodes\Keywords\LinkedInKeyword;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LinkedInKeywordTest extends TestCase
{
	private LinkedInKeyword $keyword;

	protected function setUp(): void
	{
		$this->keyword = new LinkedInKeyword();
	}

	#[Test]
	public function processShortcodeCreatesLinkedInEmbed(): void
	{
		$result = $this->keyword->processShortcode(
			'linkedin',
			['value' => 'urn:li:share:1234567890'],
			'[linkedin=urn:li:share:1234567890]'
		);

		self::assertStringContainsString('<div class="shortcode linkedin post"', $result);
		self::assertStringContainsString('<iframe src="https://www.linkedin.com/embed/feed/update/', $result);
		self::assertStringContainsString('allowfullscreen', $result);
	}

	#[Test]
	public function processShortcodeExtractsUrnFromUrl(): void
	{
		$result = $this->keyword->processShortcode(
			'linkedin',
			['value' => 'https://www.linkedin.com/posts/user_urn:li:share:1234567890'],
			'[linkedin=https://www.linkedin.com/posts/user_urn:li:share:1234567890]'
		);

		// The LinkedIn logic extracts the last segment after splitting by /
		self::assertStringContainsString('/update/user_urn:li:share:1234567890', $result);
	}

	#[Test]
	public function processShortcodeUsesDefaultDimensions(): void
	{
		$result = $this->keyword->processShortcode(
			'linkedin',
			['value' => 'urn:li:share:1234567890'],
			'[linkedin=urn:li:share:1234567890]'
		);

		self::assertStringContainsString('width="100%"', $result);
		self::assertStringContainsString('height="600"', $result);
	}

	#[Test]
	public function processShortcodeAllowsCustomDimensions(): void
	{
		$result = $this->keyword->processShortcode(
			'linkedin',
			['value' => 'urn:li:share:1234567890', 'width' => '800', 'height' => '400'],
			'[linkedin=urn:li:share:1234567890 width="800" height="400"]'
		);

		self::assertStringContainsString('width="800"', $result);
		self::assertStringContainsString('height="400"', $result);
	}

	#[Test]
	public function processShortcodeIncludesLazyLoadingByDefault(): void
	{
		$result = $this->keyword->processShortcode(
			'linkedin',
			['value' => 'urn:li:share:1234567890'],
			'[linkedin=urn:li:share:1234567890]'
		);

		self::assertStringContainsString('loading="lazy"', $result);
	}

	#[Test]
	public function processShortcodeIncludesTitle(): void
	{
		$result = $this->keyword->processShortcode(
			'linkedin',
			['value' => 'urn:li:share:1234567890', 'title' => 'LinkedIn Post'],
			'[linkedin=urn:li:share:1234567890 title="LinkedIn Post"]'
		);

		self::assertStringContainsString('title="LinkedIn Post"', $result);
	}

	#[Test]
	public function allowedAttributesIncludesHeightWidthAndLoading(): void
	{
		$reflection = new \ReflectionClass($this->keyword);
		$property = $reflection->getProperty('attributes');

		$attributes = $property->getValue($this->keyword);

		self::assertContains('height', $attributes);
		self::assertContains('width', $attributes);
		self::assertContains('loading', $attributes);
	}
}
