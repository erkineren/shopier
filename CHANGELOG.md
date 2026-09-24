# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Improved documentation with English translations
- PHPUnit testing framework
- PHP_CodeSniffer for code quality
- Proper open source file structure
- Test coverage for signature calculation, response validation and all renderers
- Dependabot configuration for GitHub Actions and Composer dependencies
- `.gitattributes` to keep tests, examples and CI files out of Composer dist archives

### Changed

- CI now runs on PHP 7.2 – 8.5 using `actions/checkout@v4` and `actions/cache@v4`
- PHPUnit 9.6 is now allowed alongside 8.5 so tests run on modern PHP versions
- `composer.lock` is no longer committed (library best practice)
- `IframeRenderer` now submits the form into a named iframe instead of writing HTML
  into the iframe via JavaScript; numeric width/height values are treated as pixels
- `ShopierParams` uses `random_int()` instead of `rand()` for `random_nr`

### Fixed

- PHP 8.4 deprecation: implicitly nullable `$shopierParams` parameter in `Shopier::__construct()`
- Form field values, the form action and button attributes are now HTML-escaped,
  preventing broken markup and XSS when values contain quotes or HTML
- Response signatures are compared with `hash_equals()` to prevent timing attacks
- `ShopierButtonRenderer::setName()` had no effect; it now updates the button text and is chainable
- Examples and README used `getenv()` which is not populated by phpdotenv; they now use `$_ENV`

## [1.0.0] - Initial Release

### Added

- Basic Shopier integration functionality
- Multiple renderer options
- Customizable payment flow
- Payment verification
