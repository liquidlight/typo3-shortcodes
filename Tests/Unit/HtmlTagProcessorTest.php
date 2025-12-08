<?php

declare(strict_types=1);

namespace LiquidLight\Shortcodes\Tests\Unit;

use LiquidLight\Shortcodes\Utility\HtmlTagProcessor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class HtmlTagProcessorTest extends TestCase
{
	private HtmlTagProcessor $processor;

	protected function setUp(): void
	{
		$this->processor = new HtmlTagProcessor();
	}

	#[Test]
	public function removesShortcodeFromParagraphTag(): void
	{
		$input = '<p>Text before <div class="shortcode video">shortcode content</div> text after</p>';
		$expected = '<p>Text before</p><div class="shortcode video">shortcode content</div><p>text after</p>';

		$result = $this->processor->removeInvalidShortcodeWrappers($input);

		self::assertSame($expected, $result);
	}

	#[Test]
	public function removesShortcodeFromHeadingTags(): void
	{
		$input = '<h2>Title <div class="shortcode">content</div> more</h2>';
		$expected = '<h2>Title</h2><div class="shortcode">content</div><h2>more</h2>';

		$result = $this->processor->removeInvalidShortcodeWrappers($input);

		self::assertSame($expected, $result);
	}

	#[Test]
	public function removesShortcodeFromPreTag(): void
	{
		$input = '<pre>Code <div class="shortcode">content</div> more code</pre>';
		$expected = '<pre>Code</pre><div class="shortcode">content</div><pre>more code</pre>';

		$result = $this->processor->removeInvalidShortcodeWrappers($input);

		self::assertSame($expected, $result);
	}

	#[Test]
	public function removesEmptyWrapperBeforeShortcode(): void
	{
		$input = '<p><div class="shortcode">content</div> text after</p>';
		$expected = '<div class="shortcode">content</div><p>text after</p>';

		$result = $this->processor->removeInvalidShortcodeWrappers($input);

		self::assertSame($expected, $result);
	}

	#[Test]
	public function removesEmptyWrapperAfterShortcode(): void
	{
		$input = '<p>Text before <div class="shortcode">content</div></p>';
		$expected = '<p>Text before</p><div class="shortcode">content</div>';

		$result = $this->processor->removeInvalidShortcodeWrappers($input);

		self::assertSame($expected, $result);
	}

	#[Test]
	public function removesShortcodeCompletelyIsolated(): void
	{
		$input = '<p><div class="shortcode">content</div></p>';
		$expected = '<div class="shortcode">content</div>';

		$result = $this->processor->removeInvalidShortcodeWrappers($input);

		self::assertSame($expected, $result);
	}

	#[Test]
	public function balancesInlineTagsAroundShortcode(): void
	{
		$input = '<p><strong>Bold text <div class="shortcode">content</div> more bold</strong></p>';
		$expected = '<p><strong>Bold text </strong></p><div class="shortcode">content</div><p><strong> more bold</strong></p>';

		$result = $this->processor->removeInvalidShortcodeWrappers($input);

		self::assertSame($expected, $result);
	}

	#[Test]
	public function balancesNestedInlineTags(): void
	{
		$input = '<p><strong><em>Text <div class="shortcode">content</div> more</em></strong></p>';
		$expected = '<p><strong><em>Text </em></strong></p><div class="shortcode">content</div><p><strong><em> more</em></strong></p>';

		$result = $this->processor->removeInvalidShortcodeWrappers($input);

		self::assertSame($expected, $result);
	}

	#[Test]
	public function preservesInlineTagAttributes(): void
	{
		$input = '<p><a href="/page">Link <div class="shortcode">content</div> text</a></p>';
		$expected = '<p><a href="/page">Link </a></p><div class="shortcode">content</div><p><a href="/page"> text</a></p>';

		$result = $this->processor->removeInvalidShortcodeWrappers($input);

		self::assertSame($expected, $result);
	}

	#[Test]
	public function handlesMultipleShortcodesInSeparateTags(): void
	{
		$input = '<p>Text <div class="shortcode">first</div> middle</p><p>More <div class="shortcode">second</div> end</p>';
		$expected = '<p>Text</p><div class="shortcode">first</div><p>middle</p><p>More</p><div class="shortcode">second</div><p>end</p>';

		$result = $this->processor->removeInvalidShortcodeWrappers($input);

		self::assertSame($expected, $result);
	}

	#[Test]
	public function preservesWrapperAttributes(): void
	{
		$input = '<p class="intro">Text <div class="shortcode">content</div> more</p>';
		$expected = '<p class="intro">Text</p><div class="shortcode">content</div><p class="intro">more</p>';

		$result = $this->processor->removeInvalidShortcodeWrappers($input);

		self::assertSame($expected, $result);
	}

	#[Test]
	public function handlesShortcodeWithComplexAttributes(): void
	{
		$input = '<p>Text <div class="shortcode video youtube" data-ratio="16:9">content</div> more</p>';
		$expected = '<p>Text</p><div class="shortcode video youtube" data-ratio="16:9">content</div><p>more</p>';

		$result = $this->processor->removeInvalidShortcodeWrappers($input);

		self::assertSame($expected, $result);
	}

	#[Test]
	public function removesOnlyWhitespaceWrappers(): void
	{
		$input = '<p>   <div class="shortcode">content</div>   </p>';
		$expected = '<div class="shortcode">content</div>';

		$result = $this->processor->removeInvalidShortcodeWrappers($input);

		self::assertSame($expected, $result);
	}

	#[Test]
	public function removesNbspOnlyWrappers(): void
	{
		$input = '<p>&nbsp;<div class="shortcode">content</div>&nbsp;</p>';
		$expected = '<div class="shortcode">content</div>';

		$result = $this->processor->removeInvalidShortcodeWrappers($input);

		self::assertSame($expected, $result);
	}

	#[Test]
	public function handlesEmptyInlineTags(): void
	{
		$input = '<p><strong></strong><div class="shortcode">content</div></p>';
		$expected = '<div class="shortcode">content</div>';

		$result = $this->processor->removeInvalidShortcodeWrappers($input);

		self::assertSame($expected, $result);
	}

	#[Test]
	public function doesNotModifyValidHtml(): void
	{
		$input = '<div><p>Valid paragraph</p><div class="shortcode">content</div><p>Another paragraph</p></div>';

		$result = $this->processor->removeInvalidShortcodeWrappers($input);

		self::assertSame($input, $result);
	}

	#[Test]
	public function handlesH1ThroughH6Tags(): void
	{
		$tags = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

		foreach ($tags as $tag) {
			$input = "<{$tag}>Title <div class=\"shortcode\">content</div> more</{$tag}>";
			$expected = "<{$tag}>Title</{$tag}><div class=\"shortcode\">content</div><{$tag}>more</{$tag}>";

			$result = $this->processor->removeInvalidShortcodeWrappers($input);

			self::assertSame($expected, $result, "Failed for tag: {$tag}");
		}
	}

	#[Test]
	public function handlesMixedInlineTagsWithDifferentNesting(): void
	{
		$input = '<p><strong>Bold <em>and italic <div class="shortcode">content</div> text</em> more</strong></p>';
		$expected = '<p><strong>Bold <em>and italic </em></strong></p><div class="shortcode">content</div><p><strong><em> text</em> more</strong></p>';

		$result = $this->processor->removeInvalidShortcodeWrappers($input);

		self::assertSame($expected, $result);
	}

	#[Test]
	public function doesNotBreakOnSelfClosingInlineTags(): void
	{
		$input = '<p>Text <br /> <div class="shortcode">content</div> more</p>';
		$expected = '<p>Text <br /></p><div class="shortcode">content</div><p>more</p>';

		$result = $this->processor->removeInvalidShortcodeWrappers($input);

		self::assertSame($expected, $result);
	}

	#[Test]
	public function preservesShortcodeInternalStructure(): void
	{
		$input = '<p>Text <div class="shortcode"><iframe src="url" title="Title"></iframe></div> more</p>';
		$expected = '<p>Text</p><div class="shortcode"><iframe src="url" title="Title"></iframe></div><p>more</p>';

		$result = $this->processor->removeInvalidShortcodeWrappers($input);

		self::assertSame($expected, $result);
	}

	#[Test]
	public function handlesConsecutiveInlineTags(): void
	{
		$input = '<p><strong>Bold</strong><em>Italic</em><div class="shortcode">content</div></p>';
		$expected = '<p><strong>Bold</strong><em>Italic</em></p><div class="shortcode">content</div>';

		$result = $this->processor->removeInvalidShortcodeWrappers($input);

		self::assertSame($expected, $result);
	}

	#[Test]
	public function removesOrphanedClosingTags(): void
	{
		$input = '<p></strong><div class="shortcode">content</div> text</p>';
		$expected = '<div class="shortcode">content</div><p>text</p>';

		$result = $this->processor->removeInvalidShortcodeWrappers($input);

		self::assertSame($expected, $result);
	}

	#[Test]
	#[DataProvider('complexScenarioProvider')]
	public function handlesComplexRealWorldScenarios(string $input, string $expected): void
	{
		$result = $this->processor->removeInvalidShortcodeWrappers($input);

		self::assertSame($expected, $result);
	}

	public static function complexScenarioProvider(): array
	{
		return [
			'paragraph with link wrapping shortcode' => [
				'<p>You can watch our latest video <a href="#">[youtube code=123]</a> here</p>',
				'<p>You can watch our latest video <a href="#">[youtube code=123]</a> here</p>',
			],
			'shortcode at start of paragraph' => [
				'<p><div class="shortcode youtube">content</div> Watch this video above</p>',
				'<div class="shortcode youtube">content</div><p>Watch this video above</p>',
			],
			'shortcode at end of paragraph' => [
				'<p>Watch this video below <div class="shortcode youtube">content</div></p>',
				'<p>Watch this video below</p><div class="shortcode youtube">content</div>',
			],
			'multiple paragraphs with shortcodes' => [
				'<p>First para <div class="shortcode">one</div> text</p><p>Second para <div class="shortcode">two</div> text</p>',
				'<p>First para</p><div class="shortcode">one</div><p>text</p><p>Second para</p><div class="shortcode">two</div><p>text</p>',
			],
		];
	}
}
