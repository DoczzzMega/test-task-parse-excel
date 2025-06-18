<?php

namespace App\Jobs;

use App\Events\CountRowsEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProcessRowsFromExcelChunkJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public array $rows)
    {
        //
    }

    public function handle(): void
    {

        $toInsert = [];

        foreach ($this->rows as $row) {
            $idx = $row['row_index'];
            $id  = $row['id'];

            if (DB::table('rows')->where('id', $id)->exists()) {
                Storage::append(
                    'import_empty_rows.txt',
                    $idx . ' – duplicate id'
                );
                continue;
            }

            $toInsert[] = [
                'id'         => $id,
                'name'       => $row['name'],
                'date'       => $row['date'],
            ];
        }

        if (!empty($toInsert)) {
            DB::table('rows')->insert($toInsert);
        }
        event(new CountRowsEvent(cache('count_rows')));
    }
}
