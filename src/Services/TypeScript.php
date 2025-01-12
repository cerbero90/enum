<?php

declare(strict_types=1);

namespace Cerbero\Enum\Services;

use Cerbero\Enum\Enums;
use UnitEnum;

use function Cerbero\Enum\className;
use function Cerbero\Enum\ensureParentDirectory;

/**
 * The TypeScript service.
 */
class TypeScript
{
    /**
     * The TypeScript enums path.
     */
    protected readonly string $path;

    /**
     * Instantiate the class.
     *
     * @param class-string<UnitEnum> $enum
     */
    public function __construct(protected readonly string $enum)
    {
        $this->path = Enums::basePath(Enums::typeScript($enum));
    }

    /**
     * Synchronize the enum in TypeScript.
     */
    public function sync(bool $overwrite = false): bool
    {
        return match (true) {
            ! file_exists($this->path) => $this->createEnum(),
            $this->enumIsMissing() => $this->appendEnum(),
            $overwrite => $this->replaceEnum(),
            default => true,
        };
    }

    /**
     * Create the TypeScript file for the enum.
     */
    protected function createEnum(): bool
    {
        ensureParentDirectory($this->path);

        return file_put_contents($this->path, $this->transform()) !== false;
    }

    /**
     * Append the enum to the TypeScript file.
     */
    protected function appendEnum(): bool
    {
        return file_put_contents($this->path, PHP_EOL . $this->transform(), flags: FILE_APPEND) !== false;
    }

    /**
     * Retrieved the enum transformed for TypeScript.
     */
    public function transform(): string
    {
        $stub = (string) file_get_contents($this->stub());

        return strtr($stub, $this->replacements());
    }

    /**
     * Retrieve the path of the stub.
     */
    protected function stub(): string
    {
        return __DIR__ . '/../../stubs/typescript.stub';
    }

    /**
     * Retrieve the stub replacements.
     *
     * @return array<string, string>
     */
    protected function replacements(): array
    {
        return [
            '{{ name }}' => className($this->enum),
            '{{ cases }}' => $this->formatCases(),
        ];
    }

    /**
     * Retrieve the enum cases formatted as a string
     */
    protected function formatCases(): string
    {
        $cases = array_map(function (UnitEnum $case) {
            /** @var string|int|null $value */
            $value = is_string($value = $case->value ?? null) ? "'{$value}'" : $value;

            return "    {$case->name}" . ($value === null ? ',' : " = {$value},");
        }, $this->enum::cases());

        return implode(PHP_EOL, $cases);
    }

    /**
     * Determine whether the enum is missing.
     */
    protected function enumIsMissing(): bool
    {
        $name = className($this->enum);

        return preg_match("~^export enum {$name}~im", (string) file_get_contents($this->path)) === 0;
    }

    /**
     * Replace the enum in the TypeScript file.
     */
    protected function replaceEnum(): bool
    {
        $name = className($this->enum);
        $oldContent = (string) file_get_contents($this->path);
        $newContent = preg_replace("~^(export enum {$name}[^}]+})~im", trim($this->transform()), $oldContent);

        return file_put_contents($this->path, $newContent) !== false;
    }
}
