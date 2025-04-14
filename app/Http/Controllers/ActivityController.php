<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::all();
        return view('activities.index', compact('activities'));
    }

    public function create()
    {
        return view('activities.create');
    }

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

        $validatedData['user_id'] = auth::id();


        foreach (['photo_1', 'photo_2', 'photo_3'] as $photo) {
            if ($request->hasFile($photo)) {
                $validatedData[$photo] = $request->file($photo)->store('uploads/activities', 'public');
            }
        }

        if ($request->hasFile('bast_document')) {
            $validatedData['bast_document'] = $request->file('bast_document')->store('uploads/documents', 'public');
        }

        Activity::create($validatedData);

        return redirect()->route('activities.index')->with('success', 'Activity berhasil ditambahkan');
    }

    public function show(Activity $activity)
    {
        return view('activities.show', compact('activity'));
    }

    public function edit(Activity $activity)
    {
        return view('activities.edit', compact('activity'));
    }

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

        $activity->update($validatedData);

        return redirect()->route('activities.index')->with('success', 'Activity berhasil diperbarui!');
    }

    public function destroy(Activity $activity)
    {
        foreach (['photo_1', 'photo_2', 'photo_3', 'bast_document'] as $file) {
            if ($activity->$file) {
                Storage::disk('public')->delete($activity->$file);
            }
        }

        $activity->delete();

        return redirect()->route('activities.index')->with('success', 'Activity berhasil dihapus!');
    }
}
