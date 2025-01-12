<?php

use Cerbero\Enum\Enums;
use Cerbero\Enum\Services\Generator;

it('returns true if an enum already exists', function() {
    $outcome = (new Generator('App\Enums\Enum1', []))->generate();

    expect($outcome)->toBeTrue();
});

it('generates annotated enums', function(string $enum, ?string $backed) {
    Enums::setBasePath(__DIR__ . '/../Skeleton');
    Enums::setPaths('app/Enums', 'domain/*/Enums');

    expect(fn() => (new Generator($enum, ['CaseOne', 'CaseTwo'], $backed))->generate())->toGenerate($enum);

    (fn() => self::$paths = [])->bindTo(null, Enums::class)();
    (fn() => self::$basePath = null)->bindTo(null, Enums::class)();
})->with([
    ['App\Enums\Generated1', 'bitwise'],
    ['Domain\Common\Enums\Generated2', 'snake'],
]);

it('creates sub-directories if needed', function() {
    Enums::setBasePath(__DIR__ . '/../Skeleton');
    Enums::setPaths('app/Enums', 'domain/*/Enums');

    $enum = 'SubDirectory\Generated3';

    try {
        expect(fn() => (new Generator($enum, ['CaseOne', 'CaseTwo']))->generate())->toGenerate($enum);
    } finally {
        rmdir(Enums::basePath('SubDirectory'));
    }

    (fn() => self::$paths = [])->bindTo(null, Enums::class)();
    (fn() => self::$basePath = null)->bindTo(null, Enums::class)();
});
