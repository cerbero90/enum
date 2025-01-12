<?php

use Cerbero\Enum\Enums;

it('warns that no enums were synced if invalid enums are provided', function() {
    expect(runEnum('ts InvalidEnum'))
        ->output->toContain('No enums to synchronize.')
        ->status->toBe(0);
});

it('warns that no enums were synced if no enums can be found', function() {
    expect(runEnum('ts --all'))
        ->output->toContain('No enums to synchronize.')
        ->status->toBe(0);
});

it('synchronizes all the discoverable enums', function() {
    Enums::setBasePath(__DIR__ . '/../Skeleton');
    Enums::setPaths('app/Enums', 'domain/*/Enums');

    expect($namespaces = [...Enums::namespaces()])->toBe([
        App\Enums\Enum1::class,
        App\Enums\Enum2::class,
        Domain\Common\Enums\Enum3::class,
        Domain\Payouts\Enums\Enum4::class,
    ]);

    $enums = array_map(fn($namespace) => "\"{$namespace}\"", $namespaces);

    expect(fn() => runEnum('ts ' . implode(' ', $enums)))->toTypeScript($namespaces);

    (fn() => self::$paths = [])->bindTo(null, Enums::class)();
    (fn() => self::$basePath = null)->bindTo(null, Enums::class)();
});
