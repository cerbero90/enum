<?php

use Cerbero\Enum\Services\Annotator;

it('fails if the main trait is not used', function() {
    (new Annotator(Cerbero\Enum\Enums\Unloaded\NoTrait::class))->annotate();
})->throws('The enum Cerbero\Enum\Enums\Unloaded\NoTrait must use the trait Cerbero\Enum\Concerns\Enumerates');

it('annotates enums', function(string $enum) {
    expect(fn() => (new Annotator($enum))->annotate())->toAnnotate([$enum]);
})->with([
    App\Enums\Enum1::class,
    App\Enums\Enum2::class,
    Domain\Common\Enums\Enum3::class,
    Domain\Payouts\Enums\Enum4::class,
])->skip(PHP_OS_FAMILY == 'Windows', 'Windows is ending one line differently');

it('annotates enums overwriting existing annotations', function() {
    $enum = Domain\Common\Enums\Enum3::class;

    expect(fn() => (new Annotator($enum))->annotate(overwrite: true))
        ->toAnnotate([$enum], overwrite: true);
});
