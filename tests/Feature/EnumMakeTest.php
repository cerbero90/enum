<?php

use Cerbero\Enum\Enums;

it('fails if the enum name is not provided', function() {
    expect(runEnum('make'))
        ->output->toContain('The name of the enum is missing.')
        ->status->toBe(1);
});

it('succeeds if an enum already exists', function() {
    expect(runEnum('make App/Enums/Enum1'))
        ->output->toContain('The enum App\Enums\Enum1 already exists.')
        ->status->toBe(0);
});

it('fails if the enum cases are not provided', function() {
    expect(runEnum('make App/Enums/Test'))
        ->output->toContain('The cases of the enum are missing.')
        ->status->toBe(1);
});

it('fails if the backed option is not supported', function() {
    expect(runEnum('make App/Enums/Test one --backed=test'))
        ->output->toContain('The option --backed supports only')
        ->status->toBe(1);
});

it('generates annotated enums', function(string $enum, string $backed) {
    Enums::setBasePath(__DIR__ . '/../Skeleton');
    Enums::setPaths('app/Enums', 'domain/*/Enums');

    expect(fn() => runEnum("make \"{$enum}\" CaseOne CaseTwo --backed={$backed}"))->toGenerate($enum);

    (fn() => self::$paths = [])->bindTo(null, Enums::class)();
    (fn() => self::$basePath = null)->bindTo(null, Enums::class)();
})->with([
    ['App\Enums\Generated1', 'bitwise'],
    ['Domain\Common\Enums\Generated2', 'snake'],
])->skip(PHP_OS_FAMILY == 'Linux', 'Currently skipping for unknown behavior on Linux');
