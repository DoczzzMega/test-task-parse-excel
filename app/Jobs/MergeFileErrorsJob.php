<?php

namespace App\Jobs;

use App\Events\ImportRowsFromExcelSucceededEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use function Laravel\Prompts\warning;

class MergeFileErrorsJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    private array $filenames = [
        'import_errors.txt',
        'import_empty_rows.txt',
    ];

    public function __construct()
    {
        //
    }


    public function handle(): void
    {
        Storage::put('result.txt', '');
        info('report files start merged');
        if (! Storage::disk('local')->exists($this->filenames[0])
            && ! Storage::disk('local')->exists($this->filenames[1])) {

            info("Files have not been created yet. try number #{$this->attempts()}");

            $this->release($this->attempts() * 10);
        }

        $content1 = Storage::disk('local')->get($this->filenames[0]);

        $content2 = Storage::disk('local')->get($this->filenames[1]);

        $mergedContent = $content1 . "\n" . $content2;

        Storage::put('result.txt', $mergedContent);

        event(new ImportRowsFromExcelSucceededEvent('Импорт в базу данных завершен.'));

        info('end merge. result.txt has been generated ');

//        $this->deleteOldFiles();
    }

    public function deleteOldFiles(): void
    {
        foreach ($this->filenames as $filename) {
            Storage::delete($filename);
        }
    }
}
