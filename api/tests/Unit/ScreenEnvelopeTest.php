<?php

namespace Tests\Unit;

use App\Sdui\ScreenEnvelope;
use PHPUnit\Framework\TestCase;

class ScreenEnvelopeTest extends TestCase
{
    public function test_wrap_adds_schema_version_around_the_stac_tree(): void
    {
        $body = ['type' => 'scaffold'];

        $this->assertSame(
            [
                'schemaVersion' => ScreenEnvelope::SCHEMA_VERSION,
                'name' => 'home',
                'body' => $body,
            ],
            ScreenEnvelope::wrap('home', $body),
        );
    }
}
