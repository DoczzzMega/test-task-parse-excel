<?php

namespace App\Http\Controllers;

use App\Jobs\ImportRowsFromExcelJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use function Termwind\parse;

class LoadExcelController extends Controller
{
    public function index()
    {
        return view('load-excel');
    }

    public function store(Request $request)
    {

        $request->validate([
            'excel_file' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:204800',
            ],
        ], [
            'excel_file.required' => 'Файл обязателен к загрузке.',
            'excel_file.file'     => 'Загруженный элемент должен быть файлом.',
            'excel_file.mimes'    => 'Разрешены только файлы Excel (.xls, .xlsx).',
            'excel_file.max'      => 'Максимальный размер файла — 5 МБ.',
        ]);

        $file = $request->file('excel_file');
        $filename = time() . '_' . $file->getClientOriginalName();

        $path = $file->storeAs('excels', $filename, 'local');

        $fullPath = Storage::disk('local')->path($path);

        dispatch(new ImportRowsFromExcelJob($fullPath));

        return back()->with('success', 'Файл успешно загружен! Ожидайте');

    }

    public function getRows()
    {
        $rows = DB::table('rows')
            ->select('id', 'name', 'date')
            ->latest('date')
            ->get();

        $result = $rows->groupBy('date')->take(10)->toArray();

        return response()->json($result);
    }
}
