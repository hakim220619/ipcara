<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Daftar warna tampilan yang dapat diatur admin. Setiap warna memetakan ke satu
 * atau beberapa CSS variable di resources/css/app.css.
 */
class Theme
{
    public const PREFIX = 'theme.';

    /**
     * @return array<string, array{label: string, group: string, vars: list<string>, default: string}>
     */
    public static function colors(): array
    {
        return [
            'sidebar_background' => ['label' => 'Latar menu', 'group' => 'Menu', 'vars' => ['--sidebar-background', '--sidebar'], 'default' => '#90caf9'],
            'sidebar_foreground' => ['label' => 'Teks & ikon menu', 'group' => 'Menu', 'vars' => ['--sidebar-foreground'], 'default' => '#102541'],
            'sidebar_accent' => ['label' => 'Latar menu aktif', 'group' => 'Menu', 'vars' => ['--sidebar-accent'], 'default' => '#cde7fc'],
            'sidebar_accent_foreground' => ['label' => 'Teks menu aktif', 'group' => 'Menu', 'vars' => ['--sidebar-accent-foreground'], 'default' => '#0f2d57'],
            'sidebar_primary' => ['label' => 'Kotak logo', 'group' => 'Menu', 'vars' => ['--sidebar-primary', '--sidebar-ring'], 'default' => '#194076'],
            'sidebar_primary_foreground' => ['label' => 'Ikon logo', 'group' => 'Menu', 'vars' => ['--sidebar-primary-foreground'], 'default' => '#ffffff'],

            'primary' => ['label' => 'Warna utama (tombol, grafik)', 'group' => 'Warna Utama', 'vars' => ['--primary', '--chart-1'], 'default' => '#74c0ec'],
            'primary_foreground' => ['label' => 'Teks di atas warna utama', 'group' => 'Warna Utama', 'vars' => ['--primary-foreground'], 'default' => '#141f38'],
            'accent' => ['label' => 'Latar sorotan (label, hover)', 'group' => 'Warna Utama', 'vars' => ['--accent'], 'default' => '#e2f3fd'],
            'accent_foreground' => ['label' => 'Teks sorotan', 'group' => 'Warna Utama', 'vars' => ['--accent-foreground'], 'default' => '#0d4d73'],
            'glow' => ['label' => 'Efek cahaya', 'group' => 'Warna Utama', 'vars' => ['--glow'], 'default' => '#6ec5f7'],
            'ring' => ['label' => 'Garis fokus input', 'group' => 'Warna Utama', 'vars' => ['--ring'], 'default' => '#30a5e8'],

            'background' => ['label' => 'Latar halaman', 'group' => 'Halaman', 'vars' => ['--background'], 'default' => '#ffffff'],
            'foreground' => ['label' => 'Teks utama', 'group' => 'Halaman', 'vars' => ['--foreground', '--card-foreground', '--popover-foreground'], 'default' => '#141f38'],
            'card' => ['label' => 'Latar kartu & dialog', 'group' => 'Halaman', 'vars' => ['--card', '--popover'], 'default' => '#ffffff'],
            'secondary' => ['label' => 'Latar sekunder', 'group' => 'Halaman', 'vars' => ['--secondary', '--muted'], 'default' => '#eaf1f6'],
            'muted_foreground' => ['label' => 'Teks keterangan', 'group' => 'Halaman', 'vars' => ['--muted-foreground'], 'default' => '#5c6f8a'],
            'border' => ['label' => 'Garis tepi', 'group' => 'Halaman', 'vars' => ['--border', '--input'], 'default' => '#dae5ec'],

            'success' => ['label' => 'Tinggi / Tuntas', 'group' => 'Status Nilai', 'vars' => ['--success'], 'default' => '#1fad66'],
            'warning' => ['label' => 'Sedang', 'group' => 'Status Nilai', 'vars' => ['--warning'], 'default' => '#f59f0a'],
            'destructive' => ['label' => 'Rendah / Hapus', 'group' => 'Status Nilai', 'vars' => ['--destructive'], 'default' => '#e63333'],
        ];
    }

    /**
     * Warna yang sudah diubah admin (key => hex).
     *
     * @return array<string, string>
     */
    public static function overrides(): array
    {
        $values = Setting::allValues();
        $result = [];

        foreach (array_keys(static::colors()) as $key) {
            $value = $values[static::PREFIX.$key] ?? null;

            if ($value !== null && static::isHex($value)) {
                $result[$key] = strtolower($value);
            }
        }

        return $result;
    }

    /**
     * CSS variable yang ditimpa (nama => hex), hanya untuk warna yang berbeda dari bawaan.
     *
     * @return array<string, string>
     */
    public static function cssVariables(): array
    {
        $colors = static::colors();
        $vars = [];

        foreach (static::overrides() as $key => $hex) {
            foreach ($colors[$key]['vars'] as $var) {
                $vars[$var] = $hex;
            }
        }

        return $vars;
    }

    public static function css(): string
    {
        $vars = static::cssVariables();

        if ($vars === []) {
            return '';
        }

        $body = implode(';', array_map(fn ($var, $hex) => "{$var}:{$hex}", array_keys($vars), $vars));

        return ":root:root{{$body}}";
    }

    public static function isHex(string $value): bool
    {
        return (bool) preg_match('/^#[0-9a-fA-F]{6}$/', $value);
    }
}
