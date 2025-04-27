<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function __construct()
    {
        // Middleware untuk mengatur permission berdasarkan aksi
        $this->middleware('permission:view activity', ['only' => ['index']]);
        $this->middleware('permission:create activity', ['only' => ['create', 'store']]);
        $this->middleware('permission:update activity', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete activity', ['only' => ['destroy']]);
    }

    // Menampilkan daftar activities
    public function index()
    {
        $activities = Activity::all();
        return view('activities.index', compact('activities'));
    }

    // Menampilkan form untuk membuat activity baru
    public function create()
    {
        return view('activities.create');
    }

    // Menyimpan activity baru
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'no_unit_tiket' => 'required|string',
            'job_description' => 'required|string',
            'task_description' => 'required|string',
            'hour_meter' => 'required|string',
            'category_issues' => 'required|string',
            'priority' => 'required|string',
            'pic_assignee' => 'required|string',
            'time_in' => 'required|date',
            'time_out' => 'required|date',
            'status' => 'required|string',
            'price' => 'required|numeric',
            'part_number' => 'required|string',
            'part_name' => 'required|string',
            'part_description' => 'required|string',
            'stock_in' => 'required|integer',
            'stock_out' => 'required|integer',
            'price_stock' => 'required|numeric',
            'final_stock' => 'required|integer',
            'photo_1' => 'nullable|image',
            'photo_2' => 'nullable|image',
            'photo_3' => 'nullable|image',
            'bast_document' => 'nullable|file|mimes:pdf,doc,docx',
        ]);

        $validatedData['user_id'] = Auth::id();

        // Proses upload file
        foreach (['photo_1', 'photo_2', 'photo_3'] as $photo) {
            if ($request->hasFile($photo)) {
                $validatedData[$photo] = $request->file($photo)->store('uploads/activities', 'public');
            }
        }

        if ($request->hasFile('bast_document')) {
            $validatedData['bast_document'] = $request->file('bast_document')->store('uploads/documents', 'public');
        }

        // Simpan data activity
        Activity::create($validatedData);

        // Redirect ke admin/activities setelah sukses
        return redirect()->route('admin.activities.index')->with('success', 'Activity berhasil ditambahkan');
    }

    // Menampilkan detail activity
    public function show(Activity $activity)
    {
        return view('activities.show', compact('activity'));
    }

    // Menampilkan form untuk mengedit activity
    public function edit(Activity $activity)
    {
        return view('activities.edit', compact('activity'));
    }

    // Memperbarui activity
    public function update(Request $request, Activity $activity)
    {
        $validatedData = $request->validate([
            'no_unit_tiket' => 'required|string',
            'job_description' => 'required|string',
            'task_description' => 'required|string',
            'hour_meter' => 'required|string',
            'category_issues' => 'required|string',
            'priority' => 'required|string',
            'pic_assignee' => 'required|string',
            'time_in' => 'required|date',
            'time_out' => 'required|date',
            'status' => 'required|string',
            'price' => 'required|numeric',
            'part_number' => 'nullable|string',
            'part_name' => 'nullable|string',
            'part_description' => 'nullable|string',
            'stock_in' => 'nullable|numeric',
            'stock_out' => 'nullable|numeric',
            'final_stock' => 'nullable|numeric',
            'price_stock' => 'nullable|numeric',
            'photo_1' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'photo_2' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'photo_3' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'bast_document' => 'nullable|file|mimes:pdf|max:5120',
            'user_id' => 'required|exists:users,id',
        ]);

        // Proses upload file, jika ada perubahan file, hapus file lama
        foreach (['photo_1', 'photo_2', 'photo_3'] as $file) {
            if ($request->hasFile($file)) {
                if ($activity->$file) {
                    Storage::disk('public')->delete($activity->$file);
                }
                $validatedData[$file] = $request->file($file)->store('uploads/activities', 'public');
            }
        }

        if ($request->hasFile('bast_document')) {
            if ($activity->bast_document) {
                Storage::disk('public')->delete($activity->bast_document);
            }
            $validatedData['bast_document'] = $request->file('bast_document')->store('uploads/documents', 'public');
        }

        // Update data activity
        $activity->update($validatedData);

        // Redirect ke admin/activities setelah sukses
        return redirect()->route('admin.activities.index')->with('success', 'Activity berhasil diperbarui!');
    }

    // Menghapus activity
    public function destroy(Activity $activity)
    {
        // Hapus file jika ada
        foreach (['photo_1', 'photo_2', 'photo_3', 'bast_document'] as $file) {
            if ($activity->$file) {
                Storage::disk('public')->delete($activity->$file);
            }
        }

        // Hapus activity
        $activity->delete();

        // Redirect ke admin/activities setelah sukses
        return redirect()->route('admin.activities.index')->with('success', 'Activity berhasil dihapus!');
    }
}
