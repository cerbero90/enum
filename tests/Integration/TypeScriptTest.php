<?php

use Cerbero\Enum\Enums;
use Cerbero\Enum\Services\TypeScript;

use function Cerbero\Enum\className;

it('creates and appends enums', function() {
    Enums::setBasePath(__DIR__ . '/../Skeleton');

    $enums = [
        App\Enums\Enum1::class,
        App\Enums\Enum2::class,
        Domain\Common\Enums\Enum3::class,
    ];

    foreach ($enums as $enum) {
        expect((new TypeScript($enum))->sync())->toBeTrue();
    }

    expect(fn() => (new TypeScript('Domain\Payouts\Enums\Enum4'))->sync())->toTypeScript([$enum]);

    (fn() => self::$basePath = null)->bindTo(null, Enums::class)();
});

it('replaces enums', function() {
    Enums::setBasePath(__DIR__ . '/../Skeleton');

    $enums = [
        App\Enums\Enum1::class,
        App\Enums\Enum2::class,
        Domain\Common\Enums\Enum3::class,
        Domain\Payouts\Enums\Enum4::class,
    ];

    foreach ($enums as $enum) {
        expect((new TypeScript($enum))->sync())->toBeTrue();
    }

    expect(fn() => (new TypeScript('Domain\Payouts\Enums\Enum4'))->sync(overwrite: true))->toTypeScript([$enum]);

    (fn() => self::$basePath = null)->bindTo(null, Enums::class)();
});

it('creates custom TypeScript files', function(string $enum) {
    Enums::setBasePath(__DIR__ . '/../Skeleton');
    Enums::setTypeScript(fn (string $enum) => 'resources/js/enums/' . className($enum) . '.ts');

    expect(fn() => (new TypeScript($enum))->sync())->toTypeScript([$enum], oneFile: false);

    (fn() => self::$typeScript = 'resources/js/enums/index.ts')->bindTo(null, Enums::class)();
    (fn() => self::$basePath = null)->bindTo(null, Enums::class)();
})->with([
    App\Enums\Enum1::class,
    App\Enums\Enum2::class,
    Domain\Common\Enums\Enum3::class,
    Domain\Payouts\Enums\Enum4::class,
]);
