# Bug

## Bug

- `ext_emconf.php` array merge missed the `['processShortcode']` key, so no keywords were registered
- Sanitise the shortcode data before processing to avoid any erroneous html tags or spaces
