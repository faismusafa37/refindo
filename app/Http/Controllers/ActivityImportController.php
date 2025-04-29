<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Project;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Session;

class ActivityImportController extends Controller
{
    // Menampilkan form upload
    public function showForm(): View
    {
        return view('activities.import'); // Pastikan view ada di resources/views/activities/import.blade.php
    }

    // Metode untuk mengimpor data dari file Excel
    public function import(Request $request)
    {
        // Validasi file yang di-upload
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);

        // Ambil file yang di-upload
        $file = $request->file('excel_file');

        // Baca file Excel menggunakan PhpSpreadsheet
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Lewati header
        $header = array_shift($rows);

        $errors = [];
        
        foreach ($rows as $index => $row) {
            // Cek apakah ada data yang kosong
            if (empty($row[0]) || empty($row[1]) || empty($row[2]) || empty($row[3]) || empty($row[4]) || empty($row[5]) || empty($row[6]) || empty($row[7])) {
                $errors[] = "Baris " . ($index + 2) . " memiliki data yang kosong.";
                continue;
            }

            // Cek dan perbaiki waktu jika tidak valid
            try {
                $time_in = \Carbon\Carbon::parse($row[6])->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                $errors[] = "Baris " . ($index + 2) . " memiliki format waktu 'time_in' yang tidak valid.";
                continue;
            }

            try {
                $time_out = \Carbon\Carbon::parse($row[7])->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                $errors[] = "Baris " . ($index + 2) . " memiliki format waktu 'time_out' yang tidak valid.";
                continue;
            }

            // Pengecekan dan proses penyimpanan data ke database
            $project = Project::where('name', $row[23])->first();
            if (!$project) {
                $errors[] = "Baris " . ($index + 2) . " tidak ditemukan project dengan nama '" . $row[23] . "'.";
                continue;
            }

            // Proses penyimpanan data ke database
            Activity::create([
                'no_unit_tiket' => $row[0],
                'job_description' => $row[1],
                'task_description' => $row[2],
                'category_issues' => $row[3],
                'priority' => $row[4],
                'pic_assignee' => $row[5],
                'time_in' => $time_in,
                'time_out' => $time_out,
                'hour_meter' => $row[8],
                'status' => $row[9],
                'price' => $row[10],
                'price_stock' => $row[11],
                'total_price' => $row[12],
                'part_number' => $row[13],
                'part_name' => $row[14],
                'part_description' => $row[15],
                'stock_in' => $row[16],
                'stock_out' => $row[17],
                'final_stock' => $row[18],
                'photo_1' => $row[19],
                'photo_2' => $row[20],
                'photo_3' => $row[21],
                'bast_document' => $row[22],
                'user_id' => Auth::id(),
                'project_id' => $project->id,
            ]);
        }

        // Jika ada error, tampilkan pesan error, jika tidak berhasil import
        if (!empty($errors)) {
            return back()->withErrors(['excel_file' => implode('<br>', $errors)]);
        }

        // Redirect setelah impor sukses
        return back()->with('success', 'Data berhasil diimpor!');
    }

    // Menambahkan metode untuk generate template Excel
    public function generateTemplate()
{
    // Membuat spreadsheet baru
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Menentukan header untuk file template
    $headers = [
        'No Unit Tiket', 'Job Description', 'Task Description', 'Category Issues', 'Priority', 'PIC Assignee', 
        'Time In', 'Time Out', 'Hour Meter', 'Status', 'Price', 'Price Stock', 'Total Price', 'Part Number',
        'Part Name', 'Part Description', 'Stock In', 'Stock Out', 'Final Stock', 'Photo 1', 'Photo 2', 'Photo 3', 'BAST Document', 'Project Name'
    ];

    // Menulis header ke spreadsheet
    $sheet->fromArray($headers, null, 'A1');

    // Menambahkan contoh data setelah header
    $exampleData = [
        'UN7594', 'Ini Contoh', 'Ini Contoh', 'Ini Contoh', 'Ini Contoh', 'Ini Contoh', 
        '2025-04-03 11:53:04', '2025-05-08 11:53:04', '43', 'Open', '116', '921', '757', 'Part Number 4',
        'Part Name 9', 'Part Description 5', '46', '19', '23', 'photo_1_6.jpg', 'photo_2_5.jpg', 'photo_3_1.jpg', 'bast_document_3.pdf', 'Sudin Selatan (Dinas LH)'
    ];

    // Menulis contoh data ke baris kedua
    $sheet->fromArray($exampleData, null, 'A2');

    // Menyimpan template Excel ke file
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    
    // Tentukan nama file untuk template
    $filename = 'template_import_activities.xlsx';

    // Menyimpan file di path sementara
    $tempFilePath = storage_path('app/public/' . $filename);
    $writer->save($tempFilePath);

    // Mendownload file template
    return response()->download($tempFilePath)->deleteFileAfterSend(true);
}
}
