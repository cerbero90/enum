# Changelog

All notable changes to `enum` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](https://keepachangelog.com/) principles.


## NEXT - YYYY-MM-DD

### Added
- Nothing

### Changed
- Nothing

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing


## 2.2.0 - 2024-11-19

### Added
- Method `SelfAware::metaAttributeNames()` to list the names of all meta attributes

### Changed
- Upgraded PHPStan to v2


## 2.1.0 - 2024-10-30

### Added
- Method has() to the cases collection
- JsonSerializable and Stringable interfaces to the cases collection
- Methods isBackedByInteger() and isBackedByString() to the SelfAware trait

### Changed
- Allow any callable when setting the logic for magic methods
- Allow meta inheritance when getting meta names
- Improve generics in cases collection
- Simplify logic by negating methods in the Compares trait

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing


## 2.0.0 - 2024-10-05

### Added
- Custom and default implementation of magic methods
- The `Meta` attribute and related methods
- Method `value()` to get the value of a backed case or the name of a pure case
- Methods `toArray()`, `map()` to the `CasesCollection`
- Generics in docblocks
- Static analysis

### Changed
- Renamed keys to meta
- `CasesCollection` methods return an instance of the collection whenever possible
- `CasesCollection::groupBy()` groups into instances of the collection
- Filtering methods keep the collection keys
- Renamed methods `CollectsCases::casesBy*()` to `CollectsCases::keyBy*()`
- Renamed `cases()` to `all()` in `CasesCollection`
- Renamed `get()` to `resolveMeta()` in `SelfAware`
- When hydrating from meta, the value is no longer mandatory and it defaults to `true`
- The value for `pluck()` is now mandatory
- Renamed sorting methods
- Introduced PER code style

### Removed
- Parameter `$default` from the `CasesCollection::first()` method


## 1.0.0 - 2022-07-12

### Added
- First implementation of the package
