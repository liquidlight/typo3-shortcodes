# 1.1.1
> 17th November 2021

## Bug

- `ext_emconf.php` array merge missed the `['processShortcode']` key, so no keywords were registered
- Sanitise the shortcode data before processing to avoid any erroneous html tags or spaces

# 1.1.0
> 17th November 2021

## Task

- Rename `shortcodes` to `typo3-shortcodes` in `composer.json`

# 1.0.0

## Feat

Release `shortcodes` TYPO3 extension with several in-built keywords
