<?php

namespace Appstract\Opcache\Test;

use Illuminate\Support\Facades\Crypt;
use Orchestra\Testbench\Attributes\WithConfig;
use PHPUnit\Framework\Attributes\Test;

#[WithConfig('opcache.enabled', false)]
class EnabledConfigTest extends TestCase
{
    #[Test]
    public function routes_return_404_when_disabled(): void
    {
        foreach (['clear', 'config', 'status', 'compile'] as $endpoint) {
            $this->get('/opcache-api/'.$endpoint)
                ->assertStatus(404);
        }
    }

    #[Test]
    public function valid_token_does_not_bypass_disabled_routes(): void
    {
        $key = Crypt::encrypt('opcache');

        $this->get('/opcache-api/status?key='.$key)
            ->assertStatus(404);
    }
}
