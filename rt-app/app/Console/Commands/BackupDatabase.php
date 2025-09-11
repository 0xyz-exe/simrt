<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--encrypt} {--upload}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create encrypted database backup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Starting database backup...');

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $filename = "backup_simrt_{$timestamp}.sql";
        $encryptedFilename = "backup_simrt_{$timestamp}.sql.enc";

        try {
            // Create database dump
            $this->createDatabaseDump($filename);

            // Encrypt if requested
            if ($this->option('encrypt')) {
                $this->encryptBackup($filename, $encryptedFilename);
                unlink(storage_path("app/backups/{$filename}"));
                $filename = $encryptedFilename;
            }

            // Upload to cloud if requested
            if ($this->option('upload')) {
                $this->uploadToCloud($filename);
            }

            // Clean old backups
            $this->cleanOldBackups();

            $this->info("✅ Backup completed: {$filename}");

        } catch (\Exception $e) {
            $this->error("❌ Backup failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function createDatabaseDump(string $filename): void
    {
        $dbConfig = config('database.connections.' . config('database.default'));

        $command = sprintf(
            'mysqldump -h%s -P%s -u%s -p%s %s > %s',
            $dbConfig['host'],
            $dbConfig['port'],
            $dbConfig['username'],
            $dbConfig['password'],
            $dbConfig['database'],
            storage_path("app/backups/{$filename}")
        );

        // Ensure backup directory exists
        if (!is_dir(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('mysqldump failed');
        }

        $this->info("📦 Database dump created: {$filename}");
    }

    private function encryptBackup(string $inputFile, string $outputFile): void
    {
        $key = config('app.backup_encryption_key', config('app.key'));
        $inputPath = storage_path("app/backups/{$inputFile}");
        $outputPath = storage_path("app/backups/{$outputFile}");

        $data = file_get_contents($inputPath);
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);

        file_put_contents($outputPath, base64_encode($iv . $encrypted));

        $this->info("🔒 Backup encrypted: {$outputFile}");
    }

    private function uploadToCloud(string $filename): void
    {
        // Upload to S3 or other cloud storage
        $localPath = storage_path("app/backups/{$filename}");
        $cloudPath = "backups/" . date('Y/m/') . $filename;

        if (Storage::disk('s3')->put($cloudPath, file_get_contents($localPath))) {
            $this->info("☁️ Backup uploaded to cloud: {$cloudPath}");
        } else {
            $this->warn("⚠️ Cloud upload failed");
        }
    }

    private function cleanOldBackups(): void
    {
        $retentionDays = config('app.backup_retention_days', 30);
        $cutoffDate = Carbon::now()->subDays($retentionDays);

        $backupDir = storage_path('app/backups');
        $files = glob("{$backupDir}/backup_simrt_*.sql*");

        foreach ($files as $file) {
            $fileTime = Carbon::createFromTimestamp(filemtime($file));

            if ($fileTime->lt($cutoffDate)) {
                unlink($file);
                $this->info("🗑️ Removed old backup: " . basename($file));
            }
        }
    }
}
