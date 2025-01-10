<?php

declare(strict_types=1);

namespace Cerbero\Enum\Services;

use Cerbero\Enum\Data\GeneratingEnum;
use Cerbero\Enum\Enums\Backed;

/**
 * The enums generator.
 */
class Generator
{
    /**
     * The enum being generated.
     */
    protected readonly GeneratingEnum $enum;

    /**
     * Instantiate the class.
     *
     * @param class-string<\UnitEnum> $namespace
     * @param string[] $cases
     * @throws \ValueError
     */
    public function __construct(string $namespace, array $cases, ?string $backed = null)
    {
        $this->enum = new GeneratingEnum($namespace, Backed::backCases($cases, $backed));
    }

    /**
     * Generate the given enum.
     */
    public function generate(bool $overwrite = false): bool
    {
        if ($this->enum->exists && ! $overwrite) {
            return true;
        }

        if (! file_exists($directory = dirname($this->enum->path))) {
            mkdir($directory, 0755, recursive: true);
        }

        $stub = (string) file_get_contents($this->stub());
        $content = strtr($stub, $this->replacements());

        return file_put_contents($this->enum->path, $content) !== false;
    }

    /**
     * Retrieve the path of the stub.
     */
    protected function stub(): string
    {
        return __DIR__ . '/../../stubs/enum.stub';
    }

    /**
     * Retrieve the replacements for the placeholders.
     *
     * @return array<string, mixed>
     */
    protected function replacements(): array
    {
        return [
            '{{ name }}' => $this->enum->name,
            '{{ namespace }}' => $this->enum->namespace,
            '{{ backingType }}' => $this->enum->backingType ? ": {$this->enum->backingType}" : '',
            '{{ cases }}' => $this->formatCases($this->enum->cases),
        ];
    }

    /**
     * Retrieve the given cases formatted as a string
     *
     * @param array<string, string|int|null> $cases
     */
    protected function formatCases(array $cases): string
    {
        $formatted = [];

        foreach ($cases as $name => $value) {
            $formattedValue = match (true) {
                is_int($value), str_contains((string) $value, '<<') => " = {$value}",
                is_string($value) => ' = ' . (str_contains($value, "'") ? "\"{$value}\"" : "'{$value}'"),
                default => '',
            };

            $formatted[] = "    case {$name}{$formattedValue};";
        }

        return implode(PHP_EOL . PHP_EOL, $formatted);
    }
}
