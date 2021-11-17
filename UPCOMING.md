# Bug

## Bug

- `ext_emconf.php` array merge missed the `['processShortcode']` key, so no keywords were registered
- `strip_tags` of the shortcode before any further regex - incase an erroneous span, div or a gets wrapped around half the value
