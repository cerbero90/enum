<?php

use Cerbero\Enum\Enums;

it('warns that no enums were annotated if invalid enums are provided', function() {
    expect(runEnum('annotate InvalidEnum'))
        ->output->toContain('No enums to annotate.')
        ->status->toBe(0);
});

it('warns that no enums were annotated if no enums can be found', function() {
    expect(runEnum('annotate --all'))
        ->output->toContain('No enums to annotate.')
        ->status->toBe(0);
});

it('displays an error message when it fails', function() {
    expect(runEnum('annotate "Cerbero\Enum\Enums\Unloaded\NoTrait"'))
        ->output->toContain('The enum Cerbero\Enum\Enums\Unloaded\NoTrait must use the trait Cerbero\Enum\Concerns\Enumerates')
        ->status->toBe(1);
});

it('annotates all the discoverable enums', function() {
    Enums::setBasePath(__DIR__ . '/../Skeleton');
    Enums::setPaths('app/Enums', 'domain/*/Enums');

    expect($namespaces = [...Enums::namespaces()])->toBe([
        App\Enums\Enum1::class,
        App\Enums\Enum2::class,
        Domain\Common\Enums\Enum3::class,
        Domain\Payouts\Enums\Enum4::class,
    ]);

    $enums = array_map(fn($namespace) => "\"{$namespace}\"", $namespaces);

    expect(fn() => runEnum('annotate ' . implode(' ', $enums)))->toAnnotate($namespaces);

    (fn() => self::$paths = [])->bindTo(null, Enums::class)();
    (fn() => self::$basePath = null)->bindTo(null, Enums::class)();
})->skip(PHP_OS_FAMILY == 'Windows', 'Windows is ending one line differently');
