<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Video;

class MakeVideosPublic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videos:make-public {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make all existing video files public in S3 storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Making video files public in S3...');
        $this->newLine();

        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        try {
            $videos = Video::whereNotNull('video_url')->get();

            if ($videos->isEmpty()) {
                $this->info('📭 No videos found with video_url');
                return;
            }

            $this->info("📹 Found {$videos->count()} videos to process");
            $this->newLine();

            $updated = 0;
            $errors = 0;
            $notFound = 0;

            $progressBar = $this->output->createProgressBar($videos->count());
            $progressBar->start();

            foreach ($videos as $video) {
                try {
                    // Extract path from URL
                    $baseUrl = Storage::url('');
                    $path = str_replace($baseUrl, '', $video->video_url);

                    // Handle different URL formats
                    if (strpos($video->video_url, 'digitaloceanspaces.com') !== false) {
                        // Extract path from DigitalOcean Spaces URL
                        $urlParts = parse_url($video->video_url);
                        $path = ltrim($urlParts['path'], '/');
                    }

                    $this->line("Checking: {$video->title} -> {$path}");

                    if (Storage::exists($path)) {
                        if (!$dryRun) {
                            // Set file visibility to public
                            Storage::setVisibility($path, 'public');
                            Log::info("Made video public: {$path}");
                        }
                        $updated++;
                    } else {
                        $notFound++;
                        if (!$dryRun) {
                            Log::warning("Video file not found: {$path}", [
                                'video_id' => $video->id,
                                'video_title' => $video->title
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    $errors++;
                    if (!$dryRun) {
                        Log::error("Error making video public", [
                            'video_id' => $video->id,
                            'video_url' => $video->video_url,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            // Results
            $this->info('📊 Results:');
            $this->table(['Status', 'Count'], [
                ['Total Videos', $videos->count()],
                [$dryRun ? 'Would Update' : 'Updated', $updated],
                ['File Not Found', $notFound],
                ['Errors', $errors],
            ]);

            if ($dryRun) {
                $this->newLine();
                $this->info('💡 Run without --dry-run to actually make changes');
            } else {
                $this->newLine();
                if ($updated > 0) {
                    $this->info("✅ Successfully made {$updated} video files public!");
                }
                if ($errors > 0) {
                    $this->error("❌ {$errors} errors occurred. Check logs for details.");
                }
                if ($notFound > 0) {
                    $this->warn("⚠️ {$notFound} video files not found in storage.");
                }
            }
        } catch (\Exception $e) {
            $this->error('❌ Command failed: ' . $e->getMessage());
            Log::error('MakeVideosPublic command failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
