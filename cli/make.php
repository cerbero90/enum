<?php

declare(strict_types=1);

use Cerbero\Enum\Enums\Backed;
use Cerbero\Enum\Services\Generator;

use function Cerbero\Enum\enumOutcome;
use function Cerbero\Enum\fail;
use function Cerbero\Enum\option;
use function Cerbero\Enum\runAnnotate;
use function Cerbero\Enum\runTs;
use function Cerbero\Enum\succeed;

if (! $enum = strtr($arguments[0] ?? '', '/', '\\')) {
    return fail('The name of the enum is missing.');
}

$force = !! array_intersect(['--force', '-f'], $options);

if (enum_exists($enum) && ! $force) {
    return succeed("The enum {$enum} already exists.");
}

if (! $cases = array_slice($arguments, 1)) {
    return fail('The cases of the enum are missing.');
}

try {
    $generator = new Generator($enum, $cases, option('backed', $options));
} catch (ValueError) {
    return fail('The option --backed supports only ' . implode(', ', Backed::names()));
}

$typeScript = !! array_intersect(['--typescript', '-t'], $options);

return enumOutcome($enum, function () use ($generator, $enum, $force, $typeScript) {
    return $generator->generate($force)
        && runAnnotate($enum, $force)
        && ($typeScript ? runTs($enum, $force) : true);
});
