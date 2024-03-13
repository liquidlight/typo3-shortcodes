# 1.8.0

**13th March 2024**

#### Feature

- Add concept of global allowed attributes
- Allow passing of `title` through to iframe-based embeds
- Add `x` to Twitter shortcode

#### Fix

- Add missing closing quote in Facebook iframe

# 1.7.0

**24th July 2023**

#### Dependencies

- Add PHP8 compatibility

# 1.6.0

**2nd June 2023**

#### Feature

- Allow `src` as iframe attribute
- Allow iframes to have `ratio` attribute

# 1.5.0

**21st March 2023**

#### Backend

- Update code to be compatible with PHP8

#### Bug

- Rename variable in iframe shortcode as it was overridden when using additional attributes

#### Chore

- Update `.gitignore` file


# 1.4.0

**27th February 2023**

#### Task

- Add lazy loading to video and other iframes (#12)

#### Bug

- Resolve Vimeo embed when used with an unlisted video (#15)
- Remove duplicate return (#14)

# 1.3.0

**29th June 2022**

#### Backend

- Change spaces in regex to `\s` to avoid ambiguity

#### Bug

- Resolve issue with shortcodes where it was ignoring spaces
- Remove shortcodes that appear within a key/valued JSON-style quote

# 1.2.2

**11th January 2022**

#### Bug

- Remove any shortcodes that appear in HTML attributes, such as meta descriptions
- Use link from WYSIWYG generated link instead of text, if there is on

# 1.2.1

**6th January 2022**

#### Bug

- Ensure page is HTML before proceeding
- Remove `debug` from middleware

# 1.2.0

**24th November 2021**

#### Bug

- Fixed issue where it was returning a JSON response as text by ignoring anything that isn't HTML
- Fixed whitespace issue with before keyword and around `=` (resolves #5)

#### Assets

- Add extension icon


# 1.1.1

**17th November 2021**

#### Bug

- `ext_emconf.php` array merge missed the `['processShortcode']` key, so no keywords were registered
- Sanitise the shortcode data before processing to avoid any erroneous html tags or spaces

# 1.1.0

**17th November 2021**

#### Task

- Rename `shortcodes` to `typo3-shortcodes` in `composer.json`

# 1.0.0

#### Feat

Release `shortcodes` TYPO3 extension with several in-built keywords
