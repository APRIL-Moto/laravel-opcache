<?php

namespace Appstract\Opcache\Test;

use Illuminate\Support\Facades\Crypt;
use PHPUnit\Framework\Attributes\Test;

class MiddlewareTest extends TestCase
{
    #[Test]
    public function allows_request_with_valid_encrypted_token(): void
    {
        $key = Crypt::encrypt('opcache');

        $this->get('/opcache-api/status?key='.$key)
            ->assertStatus(200);
    }

    #[Test]
    public function blocks_request_from_external_ip_without_token(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.1'])
            ->get('/opcache-api/status')
            ->assertStatus(403);
    }

    #[Test]
    public function blocks_request_with_invalid_token_from_external_ip(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.1'])
            ->get('/opcache-api/status?key=not-a-valid-token')
            ->assertStatus(403);
    }

    #[Test]
    public function allows_request_from_ipv4_localhost(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])
            ->get('/opcache-api/status')
            ->assertStatus(200);
    }

    #[Test]
    public function allows_request_from_ipv6_localhost(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '::1'])
            ->get('/opcache-api/status')
            ->assertStatus(200);
    }

    #[Test]
    public function allows_request_from_servers_own_ip(): void
    {
        // The server's own IP (SERVER_ADDR) is also whitelisted to allow
        // the Artisan commands to call the HTTP endpoint from the same host.
        $this->withServerVariables([
            'REMOTE_ADDR' => '10.0.0.5',
            'SERVER_ADDR' => '10.0.0.5',
        ])->get('/opcache-api/status')
            ->assertStatus(200);
    }

    #[Test]
    public function blocks_forged_cf_connecting_ip_header_from_external_ip(): void
    {
        // Regression test for the security fix: the old code read
        // $_SERVER['HTTP_CF_CONNECTING_IP'] directly, which allowed an attacker
        // reaching the origin server to forge the header and bypass the IP check.
        // $request->ip() respects TrustProxies — with no trusted proxies configured,
        // it returns the raw REMOTE_ADDR, ignoring the spoofed header.
        $this->withServerVariables([
            'REMOTE_ADDR'           => '203.0.113.1',
            'HTTP_CF_CONNECTING_IP' => '127.0.0.1',
            'HTTP_X_FORWARDED_FOR'  => '127.0.0.1',
        ])->get('/opcache-api/status')
            ->assertStatus(403);
    }
}
