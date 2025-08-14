<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TestS3Connection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:s3-connection {--detail : Show detailed output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test S3 connection and configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Testing S3 Connection...');
        $this->newLine();

        // Test 1: Configuration Check
        $this->info('1. Checking S3 Configuration...');
        $this->checkS3Config();
        $this->newLine();

        // Test 2: Basic Connection Test
        $this->info('2. Testing Basic S3 Connection...');
        $this->testBasicConnection();
        $this->newLine();

        // Test 3: File Operations Test
        $this->info('3. Testing File Operations...');
        $this->testFileOperations();
        $this->newLine();

        // Test 4: Signed URL Test
        $this->info('4. Testing Signed URL Generation...');
        $this->testSignedUrl();
        $this->newLine();

        $this->info('✅ S3 Connection Test Completed!');
    }

    private function checkS3Config()
    {
        $s3Config = config('filesystems.disks.s3');
        $defaultDisk = config('filesystems.default');

        $this->table(['Setting', 'Value', 'Status'], [
            ['Default Disk', $defaultDisk, $defaultDisk === 's3' ? '✅' : '❌'],
            ['Driver', $s3Config['driver'] ?? 'not_set', ($s3Config['driver'] ?? '') === 's3' ? '✅' : '❌'],
            ['Bucket', $s3Config['bucket'] ?? 'not_set', !empty($s3Config['bucket']) ? '✅' : '❌'],
            ['Region', $s3Config['region'] ?? 'not_set', !empty($s3Config['region']) ? '✅' : '❌'],
            ['Access Key', !empty($s3Config['key']) ? 'SET' : 'NOT_SET', !empty($s3Config['key']) ? '✅' : '❌'],
            ['Secret Key', !empty($s3Config['secret']) ? 'SET' : 'NOT_SET', !empty($s3Config['secret']) ? '✅' : '❌'],
        ]);

        if ($this->option('detail')) {
            $this->info('Environment Variables:');
            $envVars = [
                'FILESYSTEM_DRIVER' => env('FILESYSTEM_DRIVER'),
                'AWS_ACCESS_KEY_ID' => env('AWS_ACCESS_KEY_ID') ? 'SET' : 'NOT_SET',
                'AWS_SECRET_ACCESS_KEY' => env('AWS_SECRET_ACCESS_KEY') ? 'SET' : 'NOT_SET',
                'AWS_DEFAULT_REGION' => env('AWS_DEFAULT_REGION'),
                'AWS_BUCKET' => env('AWS_BUCKET'),
                'AWS_URL' => env('AWS_URL'),
            ];

            foreach ($envVars as $key => $value) {
                $this->line("  {$key}: {$value}");
            }
        }
    }

    private function testBasicConnection()
    {
        try {
            // Try to list files (this will test basic connection)
            $files = Storage::files('');
            $this->info("✅ Connection successful! Found " . count($files) . " files in root directory.");

            if ($this->option('detail') && count($files) > 0) {
                $this->info('Sample files:');
                foreach (array_slice($files, 0, 5) as $file) {
                    $this->line("  - {$file}");
                }
                if (count($files) > 5) {
                    $this->line("  ... and " . (count($files) - 5) . " more files");
                }
            }
        } catch (\Exception $e) {
            $this->error("❌ Connection failed: " . $e->getMessage());
            if ($this->option('detail')) {
                $this->error("Error details: " . $e->getTraceAsString());
            }
        }
    }

    private function testFileOperations()
    {
        $testFile = 'test/connection_test_' . time() . '.txt';
        $testContent = 'This is a test file created at ' . now()->toDateTimeString();

        try {
            // Test write
            $this->info("📝 Testing file write...");
            $writeResult = Storage::put($testFile, $testContent);

            if ($writeResult) {
                $this->info("✅ File write successful: {$testFile}");
            } else {
                $this->error("❌ File write failed");
                return;
            }

            // Test read
            $this->info("📖 Testing file read...");
            $readContent = Storage::get($testFile);

            if ($readContent === $testContent) {
                $this->info("✅ File read successful and content matches");
            } else {
                $this->error("❌ File read failed or content mismatch");
            }

            // Test exists
            $this->info("🔍 Testing file exists check...");
            $exists = Storage::exists($testFile);

            if ($exists) {
                $this->info("✅ File exists check successful");
            } else {
                $this->error("❌ File exists check failed");
            }

            // Test URL generation
            $this->info("🔗 Testing URL generation...");
            $url = Storage::url($testFile);
            $this->info("✅ URL generated: {$url}");

            // Test delete
            $this->info("🗑️ Testing file delete...");
            $deleteResult = Storage::delete($testFile);

            if ($deleteResult) {
                $this->info("✅ File delete successful");
            } else {
                $this->error("❌ File delete failed");
            }
        } catch (\Exception $e) {
            $this->error("❌ File operations failed: " . $e->getMessage());

            // Cleanup on error
            try {
                Storage::delete($testFile);
            } catch (\Exception $cleanupError) {
                // Ignore cleanup errors
            }
        }
    }

    private function testSignedUrl()
    {
        $testPath = 'videos/test_signed_url_' . time() . '.mp4';

        try {
            $this->info("🔐 Testing signed URL generation...");

            $signedUrl = Storage::temporaryUrl($testPath, now()->addMinutes(30), [
                'ResponseContentType' => 'video/mp4'
            ]);

            $this->info("✅ Signed URL generated successfully!");
            $this->info("URL length: " . strlen($signedUrl) . " characters");

            if ($this->option('detail')) {
                $this->info("Signed URL: {$signedUrl}");
            }

            // Test URL structure
            if (strpos($signedUrl, 'https://') === 0) {
                $this->info("✅ URL uses HTTPS");
            } else {
                $this->warn("⚠️ URL does not use HTTPS");
            }

            if (strpos($signedUrl, 'X-Amz-Signature') !== false) {
                $this->info("✅ URL contains AWS signature");
            } else {
                $this->error("❌ URL missing AWS signature");
            }
        } catch (\Exception $e) {
            $this->error("❌ Signed URL generation failed: " . $e->getMessage());

            if ($this->option('detail')) {
                $this->error("Error details: " . $e->getTraceAsString());
            }
        }
    }
}
