<?php

namespace App\Jobs;

use App\Models\Import;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessProductImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;
    public $tries = 1;
    public $import;

    public function __construct(Import $import)
    {
        $this->import = $import;
    }

    public function handle(): void
    {
        try {
            $this->import->update(['status' => 'processing']);

            $importerClass = $this->import->importer;
            $importer = new $importerClass($this->import);
            
            // Get all rows from the CSV
            $rows = $importer->getRows();
            
            $this->import->total_rows = $rows->count();
            $this->import->save();
            // Process each row
            foreach ($rows as $row) {
                try {
                    $record = $importer->resolveRecord();

                    if ($record && $record->save()) {
                        $this->import->successful_rows++;
                    } else {
                        $this->import->failed_rows++;
                    }

                    $this->import->processed_rows++;
                    $this->import->save();
                } catch (\Exception $e) {
                    Log::error('Row processing failed', [
                        'row' => $row,
                        'error' => $e->getMessage()
                    ]);
                    
                    $this->import->failed_rows++;
                    $this->import->processed_rows++;
                    $this->import->save();
                }
            }

            $this->import->status = 'completed';
            $this->import->save();

        } catch (\Exception $e) {
            Log::error('Import failed', [
            'import_id' => $this->import->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->import->update([
                'status' => 'failed',
                    'error' => $e->getMessage()
            ]);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Job failed', [
            'import_id' => $this->import->id,
            'error' => $exception->getMessage()
        ]);

        $this->import->update([
            'status' => 'failed',
            'error' => $exception->getMessage()
        ]);
    }
}