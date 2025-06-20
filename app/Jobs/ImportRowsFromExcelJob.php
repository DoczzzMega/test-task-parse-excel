<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use OpenSpout\Reader\XLSX\Reader;

class ImportRowsFromExcelJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 16000;

    public $failOnTimeout = true;

    protected int $chunkSize = 1000;
    public function __construct(public string $path)
    {
    }

    public function handle(): void
    {
        try {
            Storage::put('import_errors.txt', '');

            $reader = new Reader();

            if (! file_exists($this->path)) {
                return;
            }

            $reader->open($this->path);

            $batch = [];

            cache()->forget('count_rows');

            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $rowNumber => $row) {
                    $cells = $row->getCells();

                    if ($rowNumber === 1) {
                        continue;
                    }

                    $cells = $row->toArray();

                    cache()->increment('count_rows');

                    $data = [
                        'id'   => $cells[0],
                        'name' => $cells[1],
                        'date' => $cells[2],
                    ];

                    $validator = $this->validateData($data);

                    if ($validator->fails()) {
                        $messages = $validator->errors()->all();

                        $this->writeErrors($rowNumber, $messages);

                        continue;
                    }

                    $batch[] = [
                        'row_index'  => $rowNumber,
                        'id'         => $data['id'],
                        'name'       => $data['name'],
                        'date'       => Carbon::createFromFormat('d.m.Y', $data['date']),
                    ];

                    if (count($batch) === $this->chunkSize) {
                        dispatch(new ProcessRowsFromExcelChunkJob($batch));
                        $batch = [];
                    }
                }
            }

            if (!empty($batch)) {
                dispatch(new ProcessRowsFromExcelChunkJob($batch));
            }

            $reader->close();

            dispatch(new MergeFileErrorsJob());

            info('Конец парсинга');

        } catch (\Throwable $e) {
            report($e);
            $this->fail($e);
        }

    }

    public function validateData($data)
    {
        return Validator::make($data, [
            'id'   => 'required|integer|min:1',
            'name' => ['required', 'regex:/^[A-Za-z ]+$/'],
            'date' => ['required', 'date_format:d.m.Y'],
        ]);
    }

    public function writeErrors(int $rowNumber, array $messages): void
    {
        Storage::append(
            'import_errors.txt',
            $rowNumber . ' – ' . implode(', ', $messages)
        );
    }
}
