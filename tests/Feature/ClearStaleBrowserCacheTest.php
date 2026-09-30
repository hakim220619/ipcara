<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ClearStaleBrowserCacheTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function tunnel_ngrok_meminta_browser_mengosongkan_cache_sekali(): void
    {
        $this->get('https://contoh.ngrok-free.dev/login')
            ->assertOk()
            ->assertHeader('Clear-Site-Data', '"cache"')
            ->assertCookie('ipcara_cache_reset', '1', false);

        $this->withUnencryptedCookie('ipcara_cache_reset', '1')
            ->get('https://contoh.ngrok-free.dev/login')
            ->assertOk()
            ->assertHeaderMissing('Clear-Site-Data');
    }

    #[Test]
    public function domain_lokal_tidak_terpengaruh(): void
    {
        $this->get('http://localhost:9000/login')
            ->assertOk()
            ->assertHeaderMissing('Clear-Site-Data');
    }
}
