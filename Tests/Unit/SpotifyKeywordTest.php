<?php

declare(strict_types=1);

namespace LiquidLight\Shortcodes\Tests\Unit;

use LiquidLight\Shortcodes\Keywords\SpotifyKeyword;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SpotifyKeywordTest extends TestCase
{
	private SpotifyKeyword $keyword;

	protected function setUp(): void
	{
		$this->keyword = new SpotifyKeyword();
	}

	#[Test]
	public function processShortcodeCreatesSpotifyEmbed(): void
	{
		$result = $this->keyword->processShortcode(
			'spotify',
			['value' => 'https://open.spotify.com/track/123'],
			'[spotify=https://open.spotify.com/track/123]'
		);

		self::assertStringContainsString('<div class="shortcode audio spotify"', $result);
		self::assertStringContainsString('<iframe', $result);
		self::assertStringContainsString('allowfullscreen', $result);
	}

	#[Test]
	public function processShortcodeConvertsToEmbedUrl(): void
	{
		$result = $this->keyword->processShortcode(
			'spotify',
			['value' => 'https://open.spotify.com/track/abc123'],
			'[spotify=https://open.spotify.com/track/abc123]'
		);

		self::assertStringContainsString('src="https://open.spotify.com/embed/track/abc123', $result);
	}

	#[Test]
	public function processShortcodeKeepsEmbedUrlUnchanged(): void
	{
		$result = $this->keyword->processShortcode(
			'spotify',
			['value' => 'https://open.spotify.com/embed/track/xyz789'],
			'[spotify=https://open.spotify.com/embed/track/xyz789]'
		);

		self::assertStringContainsString('src="https://open.spotify.com/embed/track/xyz789', $result);
	}

	#[Test]
	public function processShortcodeRemovesQueryParameters(): void
	{
		$result = $this->keyword->processShortcode(
			'spotify',
			['value' => 'https://open.spotify.com/track/abc123?si=something'],
			'[spotify=https://open.spotify.com/track/abc123?si=something]'
		);

		self::assertStringNotContainsString('si=something', $result);
		self::assertStringContainsString('src="https://open.spotify.com/embed/track/abc123', $result);
	}

	#[Test]
	public function processShortcodeUsesDefaultHeight380(): void
	{
		$result = $this->keyword->processShortcode(
			'spotify',
			['value' => 'https://open.spotify.com/track/abc123'],
			'[spotify=https://open.spotify.com/track/abc123]'
		);

		self::assertStringContainsString('height="380"', $result);
	}

	#[Test]
	public function processShortcodeAllowsHeight80(): void
	{
		$result = $this->keyword->processShortcode(
			'spotify',
			['value' => 'https://open.spotify.com/track/abc123', 'height' => '80'],
			'[spotify=https://open.spotify.com/track/abc123 height="80"]'
		);

		self::assertStringContainsString('height="80"', $result);
	}

	#[Test]
	public function processShortcodeForcesInvalidHeightTo380(): void
	{
		$result = $this->keyword->processShortcode(
			'spotify',
			['value' => 'https://open.spotify.com/track/abc123', 'height' => '200'],
			'[spotify=https://open.spotify.com/track/abc123 height="200"]'
		);

		self::assertStringContainsString('height="380"', $result);
		self::assertStringNotContainsString('height="200"', $result);
	}

	#[Test]
	public function processShortcodeUsesDefault100PercentWidth(): void
	{
		$result = $this->keyword->processShortcode(
			'spotify',
			['value' => 'https://open.spotify.com/track/abc123'],
			'[spotify=https://open.spotify.com/track/abc123]'
		);

		self::assertStringContainsString('width="100%"', $result);
	}

	#[Test]
	public function processShortcodeAllowsCustomWidth(): void
	{
		$result = $this->keyword->processShortcode(
			'spotify',
			['value' => 'https://open.spotify.com/track/abc123', 'width' => '600'],
			'[spotify=https://open.spotify.com/track/abc123 width="600"]'
		);

		self::assertStringContainsString('width="600"', $result);
	}

	#[Test]
	public function processShortcodeIncludesLazyLoadingByDefault(): void
	{
		$result = $this->keyword->processShortcode(
			'spotify',
			['value' => 'https://open.spotify.com/track/abc123'],
			'[spotify=https://open.spotify.com/track/abc123]'
		);

		self::assertStringContainsString('loading="lazy"', $result);
	}

	#[Test]
	public function processShortcodeSupportsThemeParameter(): void
	{
		$result = $this->keyword->processShortcode(
			'spotify',
			['value' => 'https://open.spotify.com/track/abc123', 'theme' => '0'],
			'[spotify=https://open.spotify.com/track/abc123 theme="0"]'
		);

		self::assertStringContainsString('?theme=0', $result);
	}

	#[Test]
	public function processShortcodeSkipsThemeWhenNotZero(): void
	{
		$result = $this->keyword->processShortcode(
			'spotify',
			['value' => 'https://open.spotify.com/track/abc123', 'theme' => '1'],
			'[spotify=https://open.spotify.com/track/abc123 theme="1"]'
		);

		self::assertStringNotContainsString('theme=', $result);
	}

	#[Test]
	public function processShortcodeIncludesTitle(): void
	{
		$result = $this->keyword->processShortcode(
			'spotify',
			['value' => 'https://open.spotify.com/track/abc123', 'title' => 'My Playlist'],
			'[spotify=https://open.spotify.com/track/abc123 title="My Playlist"]'
		);

		self::assertStringContainsString('title="My Playlist"', $result);
	}
}
