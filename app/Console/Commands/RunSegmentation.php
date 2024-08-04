<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CustomerSegmentationController;
use App\Models\SegmentationResult;
use Illuminate\Support\Facades\Log;

class RunSegmentation extends Command
{
    protected $signature = 'segmentation:run';
    protected $description = 'Run the customer segmentation process and save results to database';

    protected $segmentationController;

    public function __construct(CustomerSegmentationController $segmentationController)
    {
        parent::__construct();
        $this->segmentationController = $segmentationController;
    }

    public function handle()
    {
        $this->info('Starting customer segmentation process...');
        
        $result = $this->segmentationController->runSegmentation();
        
        $this->info('Segmentation process completed.');
        
        // Save results to database
        try {
            SegmentationResult::create([
                'results' => json_encode($result->getData())
            ]);
            $this->info('Results saved to database.');
        } catch (\Exception $e) {
            $this->error('Failed to save results to database: ' . $e->getMessage());
            Log::error('Failed to save segmentation results: ' . $e->getMessage());
        }
        
        $this->info('Result: ' . json_encode($result->getData()));
    }
}