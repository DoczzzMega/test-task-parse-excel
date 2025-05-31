<?php

namespace App\Http\Controllers;

use App\Jobs\ImportRowsFromExcelJob;
use Illuminate\Http\Request;
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
                'max:5120',
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

//        session()->flash('success', 'Файл успешно загружен!');

        return back()->with('success', 'Файл успешно загружен!');

    }
}
