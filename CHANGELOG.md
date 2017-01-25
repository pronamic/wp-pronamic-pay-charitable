# Change Log

All notable changes to this project will be documented in this file.

This projects adheres to [Semantic Versioning](http://semver.org/) and [Keep a CHANGELOG](http://keepachangelog.com/).

## [Unreleased][unreleased]
-

## [1.1.1] - 2017-01-25
- Added filter for payment source URL and description.
- Added process_donation() method to make sure Pronamic gateway works correctly.

## [1.1.0] - 2016-11-08
- Updated gateway system to Charitable version 1.3+.

## [1.0.5] - 2016-10-20
- Added cancel URL.
- Added Pronamic gateway usage clarification
- Added transaction description setting.
- Make use of new Bancontact label and constant.
- Ensure that the filter returns a value to avoid breaking other Charitable extensions that implement their own custom templates for certain form fields.

## [1.0.4] - 2016-04-12
- Set global WordPress gateway config as default config in gateways.

## [1.0.3] - 2016-03-23
- Changed the default return URL to the campaign URL.
- Use new redirect URL filter.

## [1.0.2] - 2016-03-02
- Add support for payment methods and issuer dropdown
- Use charitable-cancelled status for cancelled payments
- Fix incorrect gateway configuration being used
- Fix default label for Bank Transfer gateway

## [1.0.1] - 2016-02-04
- Removed discontinued MiniTix gateway

## 1.0.0 - 2015-11-05
- First release.

[unreleased]: https://github.com/wp-pay-extensions/charitable/compare/1.1.1...HEAD
[1.1.1]: https://github.com/wp-pay-extensions/charitable/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/wp-pay-extensions/charitable/compare/1.0.5...1.1.0
[1.0.5]: https://github.com/wp-pay-extensions/charitable/compare/1.0.4...1.0.5
[1.0.4]: https://github.com/wp-pay-extensions/charitable/compare/1.0.3...1.0.4
[1.0.3]: https://github.com/wp-pay-extensions/charitable/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/wp-pay-extensions/charitable/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/wp-pay-extensions/charitable/compare/1.0.0...1.0.1
