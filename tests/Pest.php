<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

// uses(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

use Cerbero\Enum\Enums;

use function Cerbero\Enum\className;
use function Cerbero\Enum\cli;
use function Cerbero\Enum\namespaceToPath;
use function Cerbero\Enum\path;

expect()->extend('toAnnotate', function (array $enums, bool $overwrite = false) {
    $oldContents = [];

    foreach ($enums as $enum) {
        $filename = (new ReflectionEnum($enum))->getFileName();

        $oldContents[$filename] = file_get_contents($filename);
    }

    try {
        expect($this->value)->toProcessEnums($enums);

        foreach ($oldContents as $filename => $oldContent) {
            $stub = __DIR__ . '/stubs/annotate/' . basename($filename, '.php') . '.stub';

            if ($overwrite && file_exists($path = __DIR__ . '/stubs/' . basename($filename, '.php') . '.force.stub')) {
                $stub = $path;
            }

            expect($filename)->toContainIgnoreEol($stub);
        }
    } finally {
        foreach ($oldContents as $filename => $oldContent) {
            file_put_contents($filename, $oldContent);
        }
    }
});

expect()->extend('toProcessEnums', function (array $enums) {
    $value = ($this->value)();

    if (is_bool($value)) {
        expect($value)->toBeTrue();
    } else {
        expect($value)
            ->output->toContain(...$enums)
            ->status->toBe(0);
    }
});

expect()->extend('toContainIgnoreEol', function (string $path) {
    // normalize content to avoid end-of-line incompatibility between OS
    $actualContent = str_replace("\r\n", "\n", file_get_contents(path($this->value)));
    $expectedContent = str_replace("\r\n", "\n", file_get_contents(path($path)));

    expect($actualContent)->toBe($expectedContent);
});

expect()->extend('toGenerate', function (string $enum, bool $cli = false) {
    expect(class_exists($enum))->toBeFalse();

    try {
        expect($this->value)->toProcessEnums([$enum]);

        $filename = namespaceToPath($enum);
        $directory = $cli ? 'make' : 'generator';
        $stub = sprintf('%s/stubs/%s/%s.stub', __DIR__, $directory, className($enum));

        expect($filename)->toContainIgnoreEol($stub);
    } finally {
        file_exists($filename) && unlink($filename);
    }
});

expect()->extend('toTypeScript', function (array $enums, bool $oneFile = true) {
    expect($this->value)->toProcessEnums($enums);

    $paths = [];

    try {
        foreach ($enums as $enum) {
            $paths[] = $path = Enums::basePath(Enums::typeScript($enum));
            $stub = sprintf('%s/stubs/ts/%s.stub', __DIR__, $oneFile ? 'enums' : className($enum));

            expect($path)->toContainIgnoreEol($stub);
        }
    } finally {
        foreach ($paths as $path) {
            file_exists($path) && unlink($path);
        }
    }
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Retrieve the outcome of the given enum console command.
 *
 * @return object{output: string, status: int}
 */
function runEnum(string $command): stdClass
{
    $paths = sprintf('--base-path="%s" --paths="%s"', Enums::basePath(), implode(',', Enums::paths()));

    ob_start();

    cli("{$command} {$paths}", $status);

    $output = ob_get_clean();

    return (object) compact('output', 'status');
}
