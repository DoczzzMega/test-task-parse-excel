<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Загрузка Excel-файла</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body>
<div class="container py-5">
    <h1>Загрузить Excel-файл</h1>
    <h6
        x-data="showCountRows()"
        x-show="visible"
        x-cloak>
        Число обработанных строк
        <span x-text="count"></span>
    </h6>

    <div x-data="finalSuccessAlert()"
         x-show="visible"
         x-cloak
         class="alert alert-success alert-dismissible" role="alert">
        <button @click="visible = false" type="button" class="btn-close" data-bs-dismiss="alert"
                aria-label="Close"></button>
        <span x-text="message"></span>
    </div>

    @if(session('success'))
        <div x-data="successUpload()"
             x-show="visible" class="alert alert-warning">
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
            <label for="excel_file" class="form-label">Выберите Excel-файл (.xlsx):</label>
            <input class="form-control" type="file" id="excel_file" name="excel_file" accept=".xls,.xlsx">
        </div>

        <button type="submit" class="btn btn-primary">Загрузить</button>
    </form>
</div>
<script>
    function finalSuccessAlert() {
        return {
            visible: false,
            message: '',

            init() {
                window.Echo.channel('uploadExcel')
                    .listen('ImportRowsFromExcelSucceededEvent', (e) => {
                        console.log(e.message)
                        this.message = e.message;
                        this.visible = true;
                    });
            }
        }
    }
    function successUpload() {
        return {
            visible: true,
            init() {
                setTimeout(() => {
                    this.visible = false;
                }, 5000);
            }
        }
    }
    function showCountRows() {
        return {
            count: 0,
            visible: false,
            init() {
                window.Echo.channel('count-rows')
                    .listen('CountRowsEvent', (e) => {
                        console.log(e.count)
                        this.count = e.count
                        this.visible = true
                    });
            }
        }
    }
</script>
</body>
</html>
