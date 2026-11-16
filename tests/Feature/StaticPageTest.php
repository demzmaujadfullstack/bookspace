<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StaticPageTest extends TestCase
{
    use RefreshDatabase;

    public static function pageProvider(): array
    {
        return [
            'tentang' => ['pages.about', 'Tentang Kami'],
            'kebijakan privasi' => ['pages.privacy', 'Kebijakan Privasi'],
            'syarat ketentuan' => ['pages.terms', 'Syarat & Ketentuan'],
        ];
    }

    #[DataProvider('pageProvider')]
    public function test_static_page_is_publicly_available(string $route, string $heading): void
    {
        $this->get(route($route))
            ->assertStatus(200)
            ->assertSee($heading);
    }

    public function test_footer_links_point_to_the_static_pages(): void
    {
        $content = $this->get('/')->assertStatus(200)->getContent();

        foreach (['/tentang', '/kebijakan-privasi', '/syarat-ketentuan'] as $path) {
            $this->assertStringContainsString($path, $content, "Tautan {$path} hilang dari footer.");
        }
    }
}
