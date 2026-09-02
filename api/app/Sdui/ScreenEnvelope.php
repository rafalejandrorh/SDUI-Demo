<?php

namespace App\Sdui;

final class ScreenEnvelope
{
    public const int SCHEMA_VERSION = 1;

    /**
     * Wrap a Core/Stac widget tree for HTTP. Builders stay unchanged.
     *
     * @param  array<string, mixed>  $body
     * @return array{schemaVersion: int, name: string, body: array<string, mixed>}
     */
    public static function wrap(string $name, array $body): array
    {
        return [
            'schemaVersion' => self::SCHEMA_VERSION,
            'name' => $name,
            'body' => $body,
        ];
    }
}
