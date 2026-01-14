<?php

declare(strict_types=1);

namespace LiquidLight\Shortcodes\Tests\Unit;

use LiquidLight\Shortcodes\Middleware\ProcessShortcodes;
use LiquidLight\Shortcodes\Utility\HtmlTagProcessor;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use ReflectionClass;

final class ProcessShortcodesTest extends TestCase
{
	private ProcessShortcodes $middleware;

	protected function setUp(): void
	{
		$this->middleware = new ProcessShortcodes();
	}

	#[Test]
	public function constructorInitializesHtmlProcessor(): void
	{
		$reflection = new ReflectionClass($this->middleware);
		$property = $reflection->getProperty('htmlProcessor');
		$property->setAccessible(true);

		$processor = $property->getValue($this->middleware);

		self::assertInstanceOf(HtmlTagProcessor::class, $processor);
	}

	#[Test]
	public function keywordConfigsPropertyIsTypedArray(): void
	{
		$reflection = new ReflectionClass($this->middleware);
		$property = $reflection->getProperty('keywordConfigs');
		$property->setAccessible(true);

		$configs = $property->getValue($this->middleware);

		self::assertIsArray($configs);
		self::assertEmpty($configs);
	}

	#[Test]
	public function isHtmlReturnsTrueForHtmlContentType(): void
	{
		$response = $this->createMock(ResponseInterface::class);
		$response->method('getHeaders')->willReturn([
			'Content-Type' => ['text/html; charset=utf-8'],
		]);

		$reflection = new ReflectionClass($this->middleware);
		$method = $reflection->getMethod('isHtml');
		$method->setAccessible(true);

		$result = $method->invoke($this->middleware, $response);

		self::assertTrue($result);
	}

	#[Test]
	public function isHtmlReturnsFalseForJsonContentType(): void
	{
		$response = $this->createMock(ResponseInterface::class);
		$response->method('getHeaders')->willReturn([
			'Content-Type' => ['application/json'],
		]);

		$reflection = new ReflectionClass($this->middleware);
		$method = $reflection->getMethod('isHtml');
		$method->setAccessible(true);

		$result = $method->invoke($this->middleware, $response);

		self::assertFalse($result);
	}

	#[Test]
	public function isHtmlReturnsFalseWhenNoContentType(): void
	{
		$response = $this->createMock(ResponseInterface::class);
		$response->method('getHeaders')->willReturn([]);

		$reflection = new ReflectionClass($this->middleware);
		$method = $reflection->getMethod('isHtml');
		$method->setAccessible(true);

		$result = $method->invoke($this->middleware, $response);

		self::assertFalse($result);
	}

	#[Test]
	public function sanitiseDataStripsHtmlTags(): void
	{
		$reflection = new ReflectionClass($this->middleware);
		$method = $reflection->getMethod('sanitiseData');
		$method->setAccessible(true);

		$result = $method->invoke($this->middleware, '<strong>Bold</strong> text');

		self::assertSame('Bold text', $result);
	}

	#[Test]
	public function sanitiseDataDecodesHtmlEntities(): void
	{
		$reflection = new ReflectionClass($this->middleware);
		$method = $reflection->getMethod('sanitiseData');
		$method->setAccessible(true);

		$result = $method->invoke($this->middleware, 'Text &amp; more');

		self::assertSame('Text & more', $result);
	}

	#[Test]
	public function sanitiseDataReplacesNonBreakingSpaces(): void
	{
		$reflection = new ReflectionClass($this->middleware);
		$method = $reflection->getMethod('sanitiseData');
		$method->setAccessible(true);

		$result = $method->invoke($this->middleware, "Text\xc2\xa0more");

		self::assertSame('Text more', $result);
	}

	#[Test]
	public function extractDataParsesKeyValuePairs(): void
	{
		$reflection = new ReflectionClass($this->middleware);
		$method = $reflection->getMethod('extractData');
		$method->setAccessible(true);

		$result = $method->invoke($this->middleware, 'youtube', 'youtube=abc123 ratio="16:9"');

		self::assertSame(['value' => 'abc123', 'ratio' => '16:9'], $result);
	}

	#[Test]
	public function extractDataConvertsColonToEquals(): void
	{
		$reflection = new ReflectionClass($this->middleware);
		$method = $reflection->getMethod('extractData');
		$method->setAccessible(true);

		$result = $method->invoke($this->middleware, 'youtube', 'youtube: abc123');

		self::assertSame(['value' => 'abc123'], $result);
	}

	#[Test]
	public function extractDataHandlesSpacesAroundEquals(): void
	{
		$reflection = new ReflectionClass($this->middleware);
		$method = $reflection->getMethod('extractData');
		$method->setAccessible(true);

		$result = $method->invoke($this->middleware, 'youtube', 'youtube = abc123');

		self::assertSame(['value' => 'abc123'], $result);
	}

	#[Test]
	public function extractDataHandlesQuotedValues(): void
	{
		$reflection = new ReflectionClass($this->middleware);
		$method = $reflection->getMethod('extractData');
		$method->setAccessible(true);

		$result = $method->invoke($this->middleware, 'youtube', 'youtube="abc123" title="My Video"');

		self::assertSame(['value' => 'abc123', 'title' => 'My Video'], $result);
	}

	#[Test]
	public function extractDataIgnoresInvalidPairs(): void
	{
		$reflection = new ReflectionClass($this->middleware);
		$method = $reflection->getMethod('extractData');
		$method->setAccessible(true);

		$result = $method->invoke($this->middleware, 'youtube', 'youtube=abc123 invalid title="Video"');

		self::assertSame(['value' => 'abc123', 'title' => 'Video'], $result);
	}

	#[Test]
	public function extractDataReplacesNbsp(): void
	{
		$reflection = new ReflectionClass($this->middleware);
		$method = $reflection->getMethod('extractData');
		$method->setAccessible(true);

		$result = $method->invoke($this->middleware, 'youtube', 'youtube=abc123&nbsp;title="Video"');

		self::assertSame(['value' => 'abc123', 'title' => 'Video'], $result);
	}

	#[Test]
	public function extractDataHandlesValuesWithSpaces(): void
	{
		$reflection = new ReflectionClass($this->middleware);
		$method = $reflection->getMethod('extractData');
		$method->setAccessible(true);

		$result = $method->invoke($this->middleware, 'youtube', 'youtube=abc123 title="My Great Video"');

		self::assertSame(['value' => 'abc123', 'title' => 'My Great Video'], $result);
	}

	#[Test]
	public function extractDataStripsQuotesFromValues(): void
	{
		$reflection = new ReflectionClass($this->middleware);
		$method = $reflection->getMethod('extractData');
		$method->setAccessible(true);

		$result = $method->invoke($this->middleware, 'youtube', 'youtube="abc123"');

		self::assertSame(['value' => 'abc123'], $result);
	}

	#[Test]
	public function extractDataHandlesSingleQuotes(): void
	{
		$reflection = new ReflectionClass($this->middleware);
		$method = $reflection->getMethod('extractData');
		$method->setAccessible(true);

		$result = $method->invoke($this->middleware, 'youtube', "youtube='abc123'");

		self::assertSame(['value' => 'abc123'], $result);
	}

	#[Test]
	public function removeShortcodesRemovesMatchingPatterns(): void
	{
		$reflection = new ReflectionClass($this->middleware);
		$method = $reflection->getMethod('removeShortcodes');
		$method->setAccessible(true);

		// Set up keyword configs to match the method's requirements
		$configProperty = $reflection->getProperty('keywordConfigs');
		$configProperty->setAccessible(true);
		$configProperty->setValue($this->middleware, ['youtube' => 'SomeClass']);

		$contents = 'text <a href="[youtube=123]">link</a> more';
		$pattern = '/(="[^"]*?)(\[youtube[^\]]*\])([^"]*?")/';

		// Use invokeArgs to properly pass by reference
		$method->invokeArgs($this->middleware, [&$contents, $pattern]);

		self::assertStringNotContainsString('[youtube=123]', $contents);
		self::assertStringContainsString('<a href="">link</a>', $contents);
	}

	#[Test]
	public function removeShortcodesHandlesMultipleOccurrences(): void
	{
		$reflection = new ReflectionClass($this->middleware);
		$method = $reflection->getMethod('removeShortcodes');
		$method->setAccessible(true);

		// Set up keyword configs
		$configProperty = $reflection->getProperty('keywordConfigs');
		$configProperty->setAccessible(true);
		$configProperty->setValue($this->middleware, ['youtube' => 'SomeClass']);

		$contents = '<a href="[youtube=1]">one</a> <a href="[youtube=2]">two</a>';
		$pattern = '/(="[^"]*?)(\[youtube[^\]]*\])([^"]*?")/';

		// Use invokeArgs to properly pass by reference
		$method->invokeArgs($this->middleware, [&$contents, $pattern]);

		self::assertStringNotContainsString('[youtube=1]', $contents);
		self::assertStringNotContainsString('[youtube=2]', $contents);
	}

	#[Test]
	public function findShortcodesReturnsEmptyArrayWhenNoMatches(): void
	{
		$reflection = new ReflectionClass($this->middleware);
		$method = $reflection->getMethod('findShortcodes');
		$method->setAccessible(true);

		// Set up empty keyword configs
		$configProperty = $reflection->getProperty('keywordConfigs');
		$configProperty->setAccessible(true);
		$configProperty->setValue($this->middleware, ['youtube' => 'SomeClass']);

		$result = $method->invoke($this->middleware, 'No shortcodes here');

		self::assertIsArray($result);
		self::assertEmpty($result[0] ?? []);
	}

	#[Test]
	public function findShortcodesFindsShortcodesWithColon(): void
	{
		$reflection = new ReflectionClass($this->middleware);
		$method = $reflection->getMethod('findShortcodes');
		$method->setAccessible(true);

		$configProperty = $reflection->getProperty('keywordConfigs');
		$configProperty->setAccessible(true);
		$configProperty->setValue($this->middleware, ['youtube' => 'SomeClass']);

		$result = $method->invoke($this->middleware, 'Text [youtube: abc123] more');

		self::assertCount(1, $result[0]);
		self::assertStringContainsString('youtube', $result[0][0]);
	}

	#[Test]
	public function findShortcodesFindsShortcodesWithEquals(): void
	{
		$reflection = new ReflectionClass($this->middleware);
		$method = $reflection->getMethod('findShortcodes');
		$method->setAccessible(true);

		$configProperty = $reflection->getProperty('keywordConfigs');
		$configProperty->setAccessible(true);
		$configProperty->setValue($this->middleware, ['youtube' => 'SomeClass']);

		$result = $method->invoke($this->middleware, 'Text [youtube=abc123] more');

		self::assertCount(1, $result[0]);
		self::assertStringContainsString('youtube', $result[0][0]);
	}

	#[Test]
	public function findShortcodesFindsShortcodesWithSpace(): void
	{
		$reflection = new ReflectionClass($this->middleware);
		$method = $reflection->getMethod('findShortcodes');
		$method->setAccessible(true);

		$configProperty = $reflection->getProperty('keywordConfigs');
		$configProperty->setAccessible(true);
		$configProperty->setValue($this->middleware, ['youtube' => 'SomeClass']);

		$result = $method->invoke($this->middleware, 'Text [youtube code=abc123] more');

		self::assertCount(1, $result[0]);
		self::assertStringContainsString('youtube', $result[0][0]);
	}

	#[Test]
	public function findShortcodesFindsMultipleShortcodes(): void
	{
		$reflection = new ReflectionClass($this->middleware);
		$method = $reflection->getMethod('findShortcodes');
		$method->setAccessible(true);

		$configProperty = $reflection->getProperty('keywordConfigs');
		$configProperty->setAccessible(true);
		$configProperty->setValue($this->middleware, ['youtube' => 'Class1', 'vimeo' => 'Class2']);

		$result = $method->invoke($this->middleware, 'Text [youtube=123] and [vimeo=456]');

		self::assertCount(2, $result[0]);
	}

	#[Test]
	public function findShortcodesIgnoresUnknownShortcodes(): void
	{
		$reflection = new ReflectionClass($this->middleware);
		$method = $reflection->getMethod('findShortcodes');
		$method->setAccessible(true);

		$configProperty = $reflection->getProperty('keywordConfigs');
		$configProperty->setAccessible(true);
		$configProperty->setValue($this->middleware, ['youtube' => 'SomeClass']);

		$result = $method->invoke($this->middleware, 'Text [unknown=123] and [youtube=456]');

		self::assertCount(1, $result[0]);
		self::assertStringContainsString('youtube', $result[0][0]);
		self::assertStringNotContainsString('unknown', $result[0][0]);
	}
}
