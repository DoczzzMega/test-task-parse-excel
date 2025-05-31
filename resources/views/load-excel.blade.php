<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Загрузка Excel-файла</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h1>Загрузить Excel-файл</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('load-excel.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="excel_file" class="form-label">Выберите Excel-файл (.xls, .xlsx):</label>
            <input class="form-control" type="file" id="excel_file" name="excel_file" accept=".xls,.xlsx">
        </div>

        <button type="submit" class="btn btn-primary">Загрузить</button>
    </form>
</div>
</body>
</html>
