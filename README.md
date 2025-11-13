# Remove Yoast SEO Comments

Remove Yoast SEO Comments - independent development repository.

## About This Repository

This is an independent development repository for the Remove Yoast SEO Comments plugin (version 3.2).

**Original Plugin:** [Remove Yoast SEO Comments](https://wordpress.org/plugins/remove-yoast-seo-comments/)
**Original Author:** Mitch (lowest)
**Original License:** GPL-2.0+

## Description

A lightweight plugin that removes the Yoast SEO advertisement HTML comments from your front-end source code.

Removes comments such as:
```html
<!-- This site is optimized with the Yoast SEO plugin -->
```

This is a must-have plugin if you have Yoast SEO installed and want cleaner HTML output.

## Features

- **Lightweight** - Minimal code, maximum efficiency
- **No Configuration** - Just activate and it works
- **No File Modifications** - Doesn't modify any Yoast SEO files
- **Automatic** - Removes comments from all pages
- **Future-Proof** - Works with all Yoast SEO versions
- **Dashboard Widget** - Shows compatibility status

## Requirements

- WordPress 4.0 or higher
- PHP 8.0 or higher
- **PHP 8.4 compatible** - Fully tested with PHP 8.4.1
- **Yoast SEO plugin** (required)

## Version Information

- **Current Version:** 3.2
- **Tested up to:** WordPress 6.7 (beta support for PHP 8.4)
- **PHP Version:** 8.0+ (fully compatible with PHP 8.4.1)
- **Compatible with:** All Yoast SEO versions

## Installation

1. Upload the plugin files to `/wp-content/plugins/remove-yoast-seo-comments/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. All done! The HTML comments are now removed from your front-end source code

No settings to configure - it just works!

## How It Works

The plugin uses WordPress hooks to remove the Yoast SEO debug markers before they are output to the page. It doesn't modify any Yoast SEO files, making it safe and update-proof.

## Compatibility

- Works with all Yoast SEO versions
- Compatible with Yoast SEO Premium
- No conflicts with Yoast SEO functionality
- Safe to use alongside other Yoast SEO extensions

## Development

This repository is maintained as an independent development fork.

- **Main branch:** Stable releases only
- **Nightly branch:** Active development (default)

All development work happens on the `nightly` branch. Only tested, stable changes are merged to `main`.

## Documentation

For original plugin documentation, visit:
- [WordPress.org Plugin Page](https://wordpress.org/plugins/remove-yoast-seo-comments/)

## License

This project maintains the GPL-2.0+ license from the upstream project.

**Development License:** CC BY-NC-ND 4.0 (for custom modifications and documentation)

See LICENSE file for details.

## Credits

- Original plugin by Mitch (lowest)
- Independent development by Ojārs Kapteinis

Co-developed with Claude AI assistance.

## Disclaimer

This is an independent development repository. For the official version and support, please visit the [original plugin page](https://wordpress.org/plugins/remove-yoast-seo-comments/).

## Note

> This plugin requires Yoast SEO to be installed and activated.
> Without Yoast SEO, this plugin has no effect.
