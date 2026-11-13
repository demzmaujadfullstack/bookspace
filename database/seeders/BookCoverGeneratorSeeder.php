<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BookCoverGeneratorSeeder extends Seeder
{
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('covers');

        $themes = [
            'laskar-pelangi' => [
                'bg1' => '#0c2461', 'bg2' => '#1e3799', 'accent' => '#fbc531',
                'motif' => 'PELANGI BELITUNG', 'icon' => '🌈',
            ],
            'bumi-manusia' => [
                'bg1' => '#2c0b0e', 'bg2' => '#5c1920', 'accent' => '#eccc68',
                'motif' => 'TETRALOGI BURU', 'icon' => '📜',
            ],
            'ayah' => [
                'bg1' => '#78281f', 'bg2' => '#3d1410', 'accent' => '#f1c40f',
                'motif' => 'KASIH KELUARGA', 'icon' => '🕊️',
            ],
            'the-alchemist' => [
                'bg1' => '#1a1c38', 'bg2' => '#0d0f22', 'accent' => '#f39c12',
                'motif' => 'PENCARIAN TAKDIR', 'icon' => '✨',
            ],
            'dilan-1990' => [
                'bg1' => '#1e3c72', 'bg2' => '#2a5298', 'accent' => '#e05c1a',
                'motif' => 'BANDUNG 1990', 'icon' => '🏍️',
            ],
            'atomic-habits' => [
                'bg1' => '#111827', 'bg2' => '#1f2937', 'accent' => '#ef4444',
                'motif' => 'PERUBAHAN 1% HARIAN', 'icon' => '⚛️',
            ],
            'the-power-of-now' => [
                'bg1' => '#1e1b4b', 'bg2' => '#0f172a', 'accent' => '#38bdf8',
                'motif' => 'KEDAMAIAN BATIN', 'icon' => '🧘',
            ],
            'rich-dad-poor-dad' => [
                'bg1' => '#311b92', 'bg2' => '#4a148c', 'accent' => '#fbbf24',
                'motif' => 'KECERDASAN FINANSIAL', 'icon' => '💰',
            ],
            'clean-code' => [
                'bg1' => '#090d16', 'bg2' => '#111827', 'accent' => '#10b981',
                'motif' => 'SOFTWARE CRAFTSMANSHIP', 'icon' => '💻',
            ],
            'laravel-up-running' => [
                'bg1' => '#7f1d1d', 'bg2' => '#450a0a', 'accent' => '#f87171',
                'motif' => 'FRAMEWORK PHP MODERN', 'icon' => '⚡',
            ],
            'matematika-untuk-sma-kelas-xii' => [
                'bg1' => '#0f2255', 'bg2' => '#172554', 'accent' => '#38bdf8',
                'motif' => 'KURIKULUM MERDEKA', 'icon' => '📐',
            ],
            'fisika-dasar' => [
                'bg1' => '#1e1b4b', 'bg2' => '#172554', 'accent' => '#a78bfa',
                'motif' => 'MEKANIKA & TERMODINAMIKA', 'icon' => '🔬',
            ],
            'zero-to-one' => [
                'bg1' => '#0f172a', 'bg2' => '#020617', 'accent' => '#e05c1a',
                'motif' => 'MEMBANGUN MASA DEPAN', 'icon' => '🚀',
            ],
            'good-to-great' => [
                'bg1' => '#881337', 'bg2' => '#4c0519', 'accent' => '#fbbf24',
                'motif' => 'DISIPLIN & KEPEMIMPINAN', 'icon' => '📈',
            ],
            'sapiens-riwayat-singkat-umat-manusia' => [
                'bg1' => '#18181b', 'bg2' => '#09090b', 'accent' => '#f59e0b',
                'motif' => 'SEJARAH HOMO SAPIENS', 'icon' => '🌍',
            ],
            'harry-potter-dan-batu-bertuah' => [
                'bg1' => '#3b0764', 'bg2' => '#1e1b4b', 'accent' => '#eab308',
                'motif' => 'DUNIA SIHIR HOGWARTS', 'icon' => '⚡',
            ],
            'si-kancil-dan-teman-temannya' => [
                'bg1' => '#14532d', 'bg2' => '#166534', 'accent' => '#fef08a',
                'motif' => 'DONGENG FABEL NUSANTARA', 'icon' => '🦌',
            ],
            'la-tahzan' => [
                'bg1' => '#064e3b', 'bg2' => '#022c22', 'accent' => '#fbbf24',
                'motif' => 'JANGAN BERSEDIH', 'icon' => '📖',
            ],
            'one-piece-vol-1' => [
                'bg1' => '#991b1b', 'bg2' => '#0f2255', 'accent' => '#fde047',
                'motif' => 'PETUALANGAN BAJAK LAUT', 'icon' => '☠️',
            ],
            'a-brief-history-of-time' => [
                'bg1' => '#020617', 'bg2' => '#0f172a', 'accent' => '#818cf8',
                'motif' => 'KOSMOLOGI & LUBANG HITAM', 'icon' => '🌌',
            ],
        ];

        $books = Book::all();

        foreach ($books as $book) {
            $slug = $book->slug;
            $theme = $themes[$slug] ?? [
                'bg1' => '#0f2255', 'bg2' => '#1e1b4b', 'accent' => '#e05c1a',
                'motif' => strtoupper($book->category->name ?? 'LITERATUR'), 'icon' => '📚',
            ];

            $escapedTitle = htmlspecialchars($book->title, ENT_QUOTES | ENT_XML1, 'UTF-8');
            $escapedAuthor = htmlspecialchars($book->author, ENT_QUOTES | ENT_XML1, 'UTF-8');
            $escapedPublisher = htmlspecialchars($book->publisher ?? 'BOOKSPACE Press', ENT_QUOTES | ENT_XML1, 'UTF-8');
            $escapedMotif = htmlspecialchars($theme['motif'], ENT_QUOTES | ENT_XML1, 'UTF-8');

            // Wrap title into 2-3 lines if long
            $words = explode(' ', $escapedTitle);
            $lines = [];
            $currentLine = '';
            foreach ($words as $w) {
                if (strlen($currentLine . ' ' . $w) > 16) {
                    if ($currentLine) $lines[] = trim($currentLine);
                    $currentLine = $w;
                } else {
                    $currentLine .= ' ' . $w;
                }
            }
            if ($currentLine) $lines[] = trim($currentLine);

            $titleSvg = '';
            $startY = 240 - (count($lines) * 16);
            foreach ($lines as $idx => $line) {
                $y = $startY + ($idx * 34);
                $titleSvg .= "<text x='200' y='{$y}' font-family='serif' font-weight='bold' font-size='26' fill='#ffffff' text-anchor='middle'>{$line}</text>";
            }

            $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 600" width="400" height="600">
  <defs>
    <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="{$theme['bg1']}"/>
      <stop offset="100%" stop-color="{$theme['bg2']}"/>
    </linearGradient>
    <linearGradient id="spine" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="rgba(0,0,0,0.55)"/>
      <stop offset="60%" stop-color="rgba(0,0,0,0.15)"/>
      <stop offset="100%" stop-color="rgba(0,0,0,0)"/>
    </linearGradient>
    <linearGradient id="accentGlow" x1="0%" y1="0%" x2="0%" y2="100%">
      <stop offset="0%" stop-color="{$theme['accent']}" stop-opacity="0.8"/>
      <stop offset="100%" stop-color="{$theme['accent']}" stop-opacity="0"/>
    </linearGradient>
  </defs>

  <!-- Background -->
  <rect width="400" height="600" fill="url(#bgGrad)"/>

  <!-- Ornamental Frame -->
  <rect x="25" y="25" width="350" height="550" fill="none" stroke="{$theme['accent']}" stroke-width="1.5" stroke-opacity="0.4" rx="4"/>
  <rect x="33" y="33" width="334" height="534" fill="none" stroke="{$theme['accent']}" stroke-width="0.8" stroke-opacity="0.25" rx="2"/>

  <!-- Top Ribbon / Motif -->
  <text x="200" y="75" font-family="sans-serif" font-size="10" font-weight="700" letter-spacing="3" fill="{$theme['accent']}" text-anchor="middle">{$escapedMotif}</text>
  <line x1="120" y1="88" x2="280" y2="88" stroke="{$theme['accent']}" stroke-width="1" stroke-opacity="0.4"/>

  <!-- Central Icon Emblem -->
  <circle cx="200" cy="140" r="36" fill="rgba(255,255,255,0.06)" stroke="{$theme['accent']}" stroke-width="1.2" stroke-opacity="0.5"/>
  <text x="200" y="148" font-size="28" text-anchor="middle">{$theme['icon']}</text>

  <!-- Title -->
  {$titleSvg}

  <!-- Divider -->
  <line x1="150" y1="380" x2="250" y2="380" stroke="{$theme['accent']}" stroke-width="1.5" stroke-opacity="0.6"/>

  <!-- Author -->
  <text x="200" y="420" font-family="serif" font-style="italic" font-size="16" fill="#f3f4f6" text-anchor="middle">karya {$escapedAuthor}</text>

  <!-- Bottom Publisher Seal -->
  <rect x="70" y="500" width="260" height="34" rx="6" fill="rgba(0,0,0,0.25)" stroke="rgba(255,255,255,0.15)" stroke-width="0.8"/>
  <text x="200" y="522" font-family="sans-serif" font-size="10" font-weight="600" letter-spacing="1.5" fill="#e5e7eb" text-anchor="middle">EDISI RESMI &bull; {$escapedPublisher}</text>

  <!-- 3D Book Spine Shadow -->
  <rect x="0" y="0" width="24" height="600" fill="url(#spine)"/>
  <!-- Book Right Edge Highlight -->
  <line x1="398" y1="0" x2="398" y2="600" stroke="rgba(255,255,255,0.35)" stroke-width="2"/>
</svg>
SVG;

            $filename = 'covers/' . $slug . '.svg';
            Storage::disk('public')->put($filename, $svg);

            $book->update(['cover' => $filename]);
        }
    }
}
