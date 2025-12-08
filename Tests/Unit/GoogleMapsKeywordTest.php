<?php

declare(strict_types=1);

namespace LiquidLight\Shortcodes\Tests\Unit;

use LiquidLight\Shortcodes\Keywords\GoogleMapsKeyword;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GoogleMapsKeywordTest extends TestCase
{
	private GoogleMapsKeyword $keyword;

	protected function setUp(): void
	{
		// Create a mock to bypass TYPO3 dependencies
		$this->keyword = $this->getMockBuilder(GoogleMapsKeyword::class)
			->disableOriginalConstructor()
			->onlyMethods([])
			->getMock()
		;

		// Set key property using reflection
		$reflection = new \ReflectionClass($this->keyword);
		$property = $reflection->getProperty('key');
		$property->setValue($this->keyword, 'test-api-key');
	}

	#[Test]
	public function processShortcodeReturnsNullWithoutApiKey(): void
	{
		$keyword = $this->getMockBuilder(GoogleMapsKeyword::class)
			->disableOriginalConstructor()
			->onlyMethods([])
			->getMock()
		;

		// Ensure key is null
		$reflection = new \ReflectionClass($keyword);
		$property = $reflection->getProperty('key');
		$property->setValue($keyword, null);

		$result = $keyword->processShortcode(
			'googlemap',
			['search' => 'London, UK'],
			'[googlemap search="London, UK"]'
		);

		self::assertNull($result);
	}

	#[Test]
	public function processShortcodeReturnsNullWithoutSearch(): void
	{
		$result = $this->keyword->processShortcode(
			'googlemap',
			[],
			'[googlemap]'
		);

		self::assertNull($result);
	}

	#[Test]
	public function processShortcodeCreatesGoogleMapsEmbed(): void
	{
		$result = $this->keyword->processShortcode(
			'googlemap',
			['search' => 'London, UK'],
			'[googlemap search="London, UK"]'
		);

		self::assertStringContainsString('<div class="shortcodes map googlemap"', $result);
		self::assertStringContainsString('<iframe src="https://www.google.com/maps/embed/v1/place?', $result);
		self::assertStringContainsString('key=test-api-key', $result);
		self::assertStringContainsString('q=London', $result);
	}

	#[Test]
	public function processShortcodeUsesDefaultMapMode(): void
	{
		$result = $this->keyword->processShortcode(
			'googlemap',
			['search' => 'Paris, France'],
			'[googlemap search="Paris, France"]'
		);

		self::assertStringContainsString('/v1/place?', $result);
	}

	#[Test]
	public function processShortcodeAllowsCustomMapMode(): void
	{
		$result = $this->keyword->processShortcode(
			'googlemap',
			['search' => 'Paris, France', 'map_mode' => 'directions'],
			'[googlemap search="Paris, France" map_mode="directions"]'
		);

		self::assertStringContainsString('/v1/directions?', $result);
	}

	#[Test]
	public function processShortcodeIncludesDefaultDimensions(): void
	{
		$result = $this->keyword->processShortcode(
			'googlemap',
			['search' => 'Berlin, Germany'],
			'[googlemap search="Berlin, Germany"]'
		);

		self::assertStringContainsString('width="100%"', $result);
		self::assertStringContainsString('height="450"', $result);
	}

	#[Test]
	public function processShortcodeAllowsCustomDimensions(): void
	{
		$result = $this->keyword->processShortcode(
			'googlemap',
			['search' => 'Berlin, Germany', 'width' => '800', 'height' => '600'],
			'[googlemap search="Berlin, Germany" width="800" height="600"]'
		);

		self::assertStringContainsString('width="800"', $result);
		self::assertStringContainsString('height="600"', $result);
	}

	#[Test]
	public function processShortcodeIncludesLazyLoadingByDefault(): void
	{
		$result = $this->keyword->processShortcode(
			'googlemap',
			['search' => 'Tokyo, Japan'],
			'[googlemap search="Tokyo, Japan"]'
		);

		self::assertStringContainsString('loading="lazy"', $result);
	}

	#[Test]
	public function processShortcodeIncludesTitle(): void
	{
		$result = $this->keyword->processShortcode(
			'googlemap',
			['search' => 'Rome, Italy', 'title' => 'Map of Rome'],
			'[googlemap search="Rome, Italy" title="Map of Rome"]'
		);

		self::assertStringContainsString('title="Map of Rome"', $result);
	}

	#[Test]
	public function processShortcodeHandlesAdditionalParameters(): void
	{
		$result = $this->keyword->processShortcode(
			'googlemap',
			['search' => 'Madrid, Spain', 'zoom' => '15', 'maptype' => 'satellite'],
			'[googlemap search="Madrid, Spain" zoom="15" maptype="satellite"]'
		);

		self::assertStringContainsString('zoom=15', $result);
		self::assertStringContainsString('maptype=satellite', $result);
	}
}
