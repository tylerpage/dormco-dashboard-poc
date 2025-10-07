<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TestS3Connection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 's3:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test S3 connection and bucket access';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing S3 connection...');

        try {
            // Test basic connection
            $disk = Storage::disk('public');
            
            // Test if we can list files (this will fail if bucket doesn't exist or credentials are wrong)
            $files = $disk->files();
            $this->info('✅ S3 connection successful!');
            $this->info('📁 Files in bucket: ' . count($files));

            // Test if we can write a test file
            $testContent = 'S3 connection test - ' . now();
            $testPath = 'test-connection-' . time() . '.txt';
            
            $disk->put($testPath, $testContent);
            $this->info('✅ File upload test successful!');

            // Test if we can read the file back
            $retrievedContent = $disk->get($testPath);
            if ($retrievedContent === $testContent) {
                $this->info('✅ File retrieval test successful!');
            } else {
                $this->error('❌ File retrieval test failed!');
                return 1;
            }

            // Test signed URL generation
            $signedUrl = $disk->temporaryUrl($testPath, now()->addMinutes(60));
            if ($signedUrl) {
                $this->info('✅ Signed URL generation successful!');
                $this->info('🔗 Test signed URL: ' . $signedUrl);
            } else {
                $this->error('❌ Signed URL generation failed!');
                return 1;
            }

            // Clean up test file
            $disk->delete($testPath);
            $this->info('🧹 Test file cleaned up');

            $this->info('');
            $this->info('🎉 All S3 tests passed! Your private bucket is ready to use.');
            
        } catch (\Exception $e) {
            $this->error('❌ S3 connection failed: ' . $e->getMessage());
            $this->error('');
            $this->error('Please check your AWS credentials in .env file:');
            $this->error('- AWS_ACCESS_KEY_ID');
            $this->error('- AWS_SECRET_ACCESS_KEY');
            $this->error('- AWS_DEFAULT_REGION');
            $this->error('- AWS_BUCKET');
            return 1;
        }

        return 0;
    }
}