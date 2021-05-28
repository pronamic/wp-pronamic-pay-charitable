# Change Log

All notable changes to this project will be documented in this file.

This projects adheres to [Semantic Versioning](http://semver.org/) and [Keep a CHANGELOG](http://keepachangelog.com/).

## [Unreleased][unreleased]
-

## [2.2.3] - 2021-05-28
- Improve using default gateway configuration.

## [2.2.2] - 2021-04-26
- Happy 2021.

## [2.2.1] - 2021-01-14
- Improved donation total amount value retrieval.

## [2.2.0] - 2021-01-14
- Use money parser for donation amount.
- Removed payment data class.
- Improved user data support, set address line 2 and country code.

## [2.1.3] - 2020-11-09
- Improved getting user data from donation.

## [2.1.2] - 2020-06-02
- Add telephone number to payment data.
- Fix error handling.

## [2.1.1] - 2020-04-03
- Fixed incorrect currency symbol filter.
- Set plugin integration name.

## [2.1.0] - 2020-03-19
- Extension extends abstract plugin integration.

## [2.0.4] - 2020-02-03
- Fixed processing decimal input amounts.

## [2.0.3] - 2019-12-22
- Improved error handling with exceptions.
- Updated usage of deprecated `addItem()` method.
- Updated payment status class name.

## [2.0.2] - 2019-08-26
- Updated packages.

## [2.0.1] - 2018-12-11
- Update item methods in payment data.

## [2.0.0] - 2018-05-14
- Switched to PHP namespaces.

## [1.1.3] - 2017-12-13
- Use default gateway if no configuration has been set.

## [1.1.2] - 2017-09-13
- Implemented `get_first_name()` and `get_last_name()`.

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

[unreleased]: https://github.com/wp-pay-extensions/charitable/compare/2.2.3...HEAD
[2.2.3]: https://github.com/wp-pay-extensions/charitable/compare/2.2.2...2.2.3
[2.2.2]: https://github.com/wp-pay-extensions/charitable/compare/2.2.1...2.2.2
[2.2.1]: https://github.com/wp-pay-extensions/charitable/compare/2.2.0...2.2.1
[2.2.0]: https://github.com/wp-pay-extensions/charitable/compare/2.1.3...2.2.0
[2.1.3]: https://github.com/wp-pay-extensions/charitable/compare/2.1.2...2.1.3
[2.1.2]: https://github.com/wp-pay-extensions/charitable/compare/2.1.1...2.1.2
[2.1.1]: https://github.com/wp-pay-extensions/charitable/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/wp-pay-extensions/charitable/compare/2.0.4...2.1.0
[2.0.4]: https://github.com/wp-pay-extensions/charitable/compare/2.0.3...2.0.4
[2.0.3]: https://github.com/wp-pay-extensions/charitable/compare/2.0.2...2.0.3
[2.0.2]: https://github.com/wp-pay-extensions/charitable/compare/2.0.1...2.0.2
[2.0.1]: https://github.com/wp-pay-extensions/charitable/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/wp-pay-extensions/charitable/compare/1.1.3...2.0.0
[1.1.3]: https://github.com/wp-pay-extensions/charitable/compare/1.1.2...1.1.3
[1.1.2]: https://github.com/wp-pay-extensions/charitable/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/wp-pay-extensions/charitable/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/wp-pay-extensions/charitable/compare/1.0.5...1.1.0
[1.0.5]: https://github.com/wp-pay-extensions/charitable/compare/1.0.4...1.0.5
[1.0.4]: https://github.com/wp-pay-extensions/charitable/compare/1.0.3...1.0.4
[1.0.3]: https://github.com/wp-pay-extensions/charitable/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/wp-pay-extensions/charitable/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/wp-pay-extensions/charitable/compare/1.0.0...1.0.1
