<?php

namespace App\Http\Controllers;

use App\Models\PartDismantle;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PartDismantleController extends Controller
{

    public function index()
    {
        $partDismantles = PartDismantle::query()
            ->when(!auth()->user()->hasRole('admin'), function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->latest()
            ->paginate(10);

        return view('part-dismantles.index', compact('partDismantles'));
    }

    public function create()
    {
        return view('part-dismantles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Your validation rules here
        ]);

        $partDismantle = PartDismantle::create($validated + [
            'user_id' => auth()->id()
        ]);

        return redirect()->route('part-dismantles.show', $partDismantle)
            ->with('success', 'Part dismantle created successfully');
    }

    public function show(PartDismantle $partDismantle)
    {
        return view('part-dismantles.show', compact('partDismantle'));
    }

    public function edit(PartDismantle $partDismantle)
    {
        return view('part-dismantles.edit', compact('partDismantle'));
    }

    public function update(Request $request, PartDismantle $partDismantle)
    {
        $validated = $request->validate([
            // Your validation rules here
        ]);

        $partDismantle->update($validated);

        return redirect()->route('part-dismantles.show', $partDismantle)
            ->with('success', 'Part dismantle updated successfully');
    }

    public function destroy(PartDismantle $partDismantle)
    {
        $partDismantle->delete();

        return redirect()->route('part-dismantles.index')
            ->with('success', 'Part dismantle deleted successfully');
    }
}