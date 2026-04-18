<?php

namespace App\Console\Commands;

use App\Models\VisitImage;
use App\Services\ImageCompressor;
use Illuminate\Console\Command;

class CompressVisitImages extends Command
{
    protected $signature = 'images:compress
                            {--quality=70 : JPEG quality (1-100)}
                            {--max=1280 : Max dimension in pixels}
                            {--dry-run : Hitung tanpa menyimpan perubahan}';

    protected $description = 'Re-compress semua foto Visit yang sudah ada di storage';

    public function handle(): int
    {
        $quality = (int) $this->option('quality');
        $max = (int) $this->option('max');
        $dryRun = (bool) $this->option('dry-run');

        $images = VisitImage::query()->whereNotNull('image_path')->get();
        $total = $images->count();

        if ($total === 0) {
            $this->warn('Tidak ada foto untuk diproses.');
            return self::SUCCESS;
        }

        $this->info("Akan memproses {$total} foto (quality={$quality}, max={$max}px)" . ($dryRun ? ' [DRY RUN]' : ''));

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $totalSaved = 0;
        $compressed = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($images as $image) {
            if ($dryRun) {
                $bar->advance();
                continue;
            }

            $saved = ImageCompressor::compressExisting(
                $image->image_path,
                'public',
                $max,
                $quality,
            );

            if ($saved === null) {
                $failed++;
            } elseif ($saved === 0) {
                $skipped++;
            } else {
                $compressed++;
                $totalSaved += $saved;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Status', 'Jumlah'],
            [
                ['Berhasil dikompres', $compressed],
                ['Dilewati (sudah optimal)', $skipped],
                ['Gagal / file tidak ditemukan', $failed],
                ['Total ukuran disimpan', $this->formatBytes($totalSaved)],
            ]
        );

        return self::SUCCESS;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return "{$bytes} B";
        }
        if ($bytes < 1024 * 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return number_format($bytes / 1024 / 1024, 2) . ' MB';
    }
}
