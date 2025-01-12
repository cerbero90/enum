<?php

declare(strict_types=1);

use Cerbero\Enum\Enums;
use Cerbero\Enum\Services\TypeScript;

use function Cerbero\Enum\enumOutcome;
use function Cerbero\Enum\normalizeEnums;
use function Cerbero\Enum\succeed;

$enums = array_intersect(['--all', '-a'], $options) ? [...Enums::namespaces()] : normalizeEnums($arguments);

if (empty($enums)) {
    return succeed('No enums to synchronize.');
}

$succeeded = true;
$force = !! array_intersect(['--force', '-f'], $options);

foreach ($enums as $enum) {
    $succeeded = enumOutcome($enum, fn() => (new TypeScript($enum))->sync($force)) && $succeeded;
}

return $succeeded;
