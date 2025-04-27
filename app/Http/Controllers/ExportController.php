<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function exportExcel()
    {
        return response()->streamDownload(function () {
            SimpleExcelWriter::streamDownload('export.xlsx')
                ->addRows(Activity::all(['id', 'name', 'date', 'status'])->toArray());
        }, 'export.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="export.xlsx"',
        ]);
    }
}
