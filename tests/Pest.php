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

expect()->extend('toAnnotate', function (array $enums, bool $overwrite = false) {
    $oldContents = [];

    foreach ($enums as $enum) {
        $filename = (new ReflectionEnum($enum))->getFileName();

        $oldContents[$filename] = file_get_contents($filename);
    }

    try {
        if (is_bool($value = ($this->value)())) {
            expect($value)->toBeTrue();
        } else {
            expect($value)
                ->output->toContain(...$enums)
                ->status->toBe(0);
        }

        foreach ($oldContents as $filename => $oldContent) {
            $stub = __DIR__ . '/stubs/' . basename($filename, '.php') . '.stub';

            if ($overwrite && file_exists($path = __DIR__ . '/stubs/' . basename($filename, '.php') . '.force.stub')) {
                $stub = $path;
            }

            expect(file_get_contents($filename))->toBe(file_get_contents($stub));
        }
    } finally {
        foreach ($oldContents as $filename => $oldContent) {
            file_put_contents($filename, $oldContent);
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
    ob_start();

    passthru(__DIR__ . "/../bin/enum {$command} 2>&1", $status);

    $output = ob_get_clean();

    return (object) compact('output', 'status');
}
