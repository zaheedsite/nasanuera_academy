<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MakeFilePublic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'file:make-public {path : The file path in S3 storage}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a specific file public in S3 storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->argument('path');

        $this->info("🔍 Making file public: {$path}");
        $this->newLine();

        try {
            // Check if file exists
            if (!Storage::exists($path)) {
                $this->error("❌ File not found: {$path}");

                // Try to list files in the directory
                $directory = dirname($path);
                $this->info("📁 Files in directory '{$directory}':");
                $files = Storage::files($directory);

                if (empty($files)) {
                    $this->warn("   No files found in directory");
                } else {
                    foreach ($files as $file) {
                        $this->line("   - {$file}");
                    }
                }

                return 1;
            }

            $this->info("✅ File found in storage");

            // Get current visibility
            try {
                $currentVisibility = Storage::getVisibility($path);
                $this->info("📋 Current visibility: {$currentVisibility}");
            } catch (\Exception $e) {
                $this->warn("⚠️ Could not get current visibility: " . $e->getMessage());
            }

            // Try to make file public
            $this->info("🔧 Setting file visibility to public...");

            try {
                // Method 1: Set visibility directly
                $result = Storage::setVisibility($path, 'public');

                if ($result) {
                    $this->info("✅ Successfully set visibility to public");
                } else {
                    $this->warn("⚠️ setVisibility returned false, trying alternative method...");

                    // Method 2: Re-upload with public ACL
                    $fileContent = Storage::get($path);
                    Storage::put($path, $fileContent, [
                        'visibility' => 'public',
                        'ACL' => 'public-read'
                    ]);

                    $this->info("✅ Re-uploaded file with public ACL");
                }

                // Verify the change
                try {
                    $newVisibility = Storage::getVisibility($path);
                    $this->info("📋 New visibility: {$newVisibility}");
                } catch (\Exception $e) {
                    $this->warn("⚠️ Could not verify new visibility: " . $e->getMessage());
                }

                // Generate public URL
                $publicUrl = Storage::url($path);
                $this->info("🔗 Public URL: {$publicUrl}");

                $this->newLine();
                $this->info("🎉 File should now be publicly accessible!");

                Log::info("Made file public via command", [
                    'path' => $path,
                    'public_url' => $publicUrl
                ]);
            } catch (\Exception $e) {
                $this->error("❌ Failed to make file public: " . $e->getMessage());

                Log::error("Failed to make file public via command", [
                    'path' => $path,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return 1;
            }
        } catch (\Exception $e) {
            $this->error("❌ Command failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
