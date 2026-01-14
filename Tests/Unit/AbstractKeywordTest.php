<?php

declare(strict_types=1);

namespace LiquidLight\Shortcodes\Tests\Unit;

use LiquidLight\Shortcodes\Keywords\AbstractKeyword;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AbstractKeywordTest extends TestCase
{
	private AbstractKeyword $keyword;

	protected function setUp(): void
	{
		$this->keyword = new class () extends AbstractKeyword {
			public function processShortcode(string $keyword, array $attributes, string $match)
			{
				return '<div>test</div>';
			}
		};
	}

	#[Test]
	public function removeAlienAttributesRemovesUnknownAttributes(): void
	{
		$attributes = [
			'value' => 'test',
			'title' => 'My Title',
			'unknown' => 'should be removed',
			'invalid' => 'also removed',
		];

		$this->keyword->removeAlienAttributes($attributes);

		self::assertArrayHasKey('value', $attributes);
		self::assertArrayHasKey('title', $attributes);
		self::assertArrayNotHasKey('unknown', $attributes);
		self::assertArrayNotHasKey('invalid', $attributes);
	}

	#[Test]
	public function removeAlienAttributesKeepsValueAttribute(): void
	{
		$attributes = ['value' => 'test123'];

		$this->keyword->removeAlienAttributes($attributes);

		self::assertArrayHasKey('value', $attributes);
		self::assertSame('test123', $attributes['value']);
	}

	#[Test]
	public function removeAlienAttributesKeepsGlobalTitleAttribute(): void
	{
		$attributes = ['title' => 'Custom Title'];

		$this->keyword->removeAlienAttributes($attributes);

		self::assertArrayHasKey('title', $attributes);
		self::assertSame('Custom Title', $attributes['title']);
	}

	#[Test]
	public function getTitleReturnsCustomTitle(): void
	{
		$reflection = new \ReflectionClass($this->keyword);
		$method = $reflection->getMethod('getTitle');

		$result = $method->invoke($this->keyword, ['title' => 'Custom Title']);

		self::assertSame('Custom Title', $result);
	}

	#[Test]
	public function getTitleIgnoresEmptyCustomTitle(): void
	{
		$reflection = new \ReflectionClass($this->keyword);
		$method = $reflection->getMethod('getTitle');

		$result = $method->invoke($this->keyword, ['title' => '']);

		self::assertNotEmpty($result);
	}

	#[Test]
	public function getTitleGeneratesFromClassName(): void
	{
		$keyword = new class () extends AbstractKeyword {
			public function processShortcode(string $keyword, array $attributes, string $match)
			{
				return '';
			}
		};

		$reflection = new \ReflectionClass($keyword);
		$method = $reflection->getMethod('getTitle');

		$result = $method->invoke($keyword, []);

		self::assertIsString($result);
		self::assertNotEmpty($result);
	}
}
