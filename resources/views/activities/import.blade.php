<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Import Excel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Import Excel Activities Refindo</h2>

        @if(session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('activities.import.process') }}" method="POST" enctype="multipart/form-data" class="mt-4">
            @csrf

            <div class="form-group">
                <label for="excel_file">Pilih File Excel (.xlsx atau .xls)</label>
                <input type="file" name="excel_file" id="excel_file" class="form-control" required>

                @error('excel_file')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-warning mt-3">Import</button>

            <!-- Tombol Generate Template -->
            <a href="{{ route('activities.generate.template') }}" class="btn btn-info mt-3 ml-2">Generate Template</a>
        </form>

        <!-- Tombol Kembali ke halaman activities -->
        <div class="mt-3">
            <a href="{{ url('/admin/activities') }}" class="btn btn-secondary">Kembali ke Halaman Kegiatan</a>
        </div>
    </div>
</body>
</html>
