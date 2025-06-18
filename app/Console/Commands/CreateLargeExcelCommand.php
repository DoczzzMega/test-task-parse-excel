<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;

class CreateLargeExcelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:excel';


    public function handle()
    {
        $sourcePath = Storage::disk('local')->path('source.xlsx');
        $targetPath = Storage::disk('local')->path('output_100mb.xlsx');

        $sourceSizeBytes = filesize($sourcePath);
        $targetSizeBytes = 100 * 1024 * 512;
        $repeatFactor = (int)ceil($targetSizeBytes / $sourceSizeBytes);

        echo "Исходный файл: {$sourceSizeBytes} байт\n";
        echo "Планируем повторить строки в {$repeatFactor} раз(а)\n";

        $reader = new Reader();
        $reader->open($sourcePath);

        $allRows = []; // [[Row], [Row], ...]
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $rowNumber => $row) {

                if ($rowNumber === 1) {
                    continue;
                }

                $cells = $row->toArray();
                $allRows[] = $cells;
            }
            // Если в файле несколько листов, и нужно только первый — после первого листа break;
            break;
        }
        $reader->close();

        echo "Всего считано строк: " . count($allRows) . "\n";

        $writer = new Writer();
        $writer->openToFile($targetPath);

        $writtenRowsCount = 0;
        for ($i = 0; $i < $repeatFactor; $i++) {
            foreach ($allRows as $cellsArray) {
                // Создаём Row из массива ячеек
                $row = Row::fromValues($cellsArray);
                $writer->addRow($row);
                $writtenRowsCount++;
            }
            echo "Пройдено итераций повторения: " . ($i + 1) . " из {$repeatFactor}\n";
        }

        $writer->close();

        echo "Готово! Всего записано строк: {$writtenRowsCount}\n";
        echo "Результат в: {$targetPath}\n";
    }
}
