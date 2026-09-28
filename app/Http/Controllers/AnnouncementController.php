<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $announcements = Announcement::latest()->paginate(10);
        return view('backend.announcement.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.announcement.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content'   => 'required|string',
            'is_active' => 'required|boolean',
        ]);

        if ($validated['is_active'] == 1) {
            Announcement::where('is_active', 1)->update(['is_active' => 0]);
        }

        Announcement::create($validated);
        return redirect()->route('announcements.index')
                         ->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $announcement = Announcement::findOrFail($id);
        return view('backend.announcement.edit', compact('announcement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $announcement = Announcement::findOrFail($id);
        $validated = $request->validate([
            'content'   => 'required|string',
            'is_active' => 'required|boolean',
        ]);
        if ($validated['is_active'] == 1) {
            Announcement::where('id', '!=', $id)->where('is_active', 1)->update(['is_active' => 0]);
        }
        $announcement->update($validated);
        return redirect()->route('announcements.index')
                         ->with('success', 'Pengumuman berhasil diperbarui.');


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();
        return redirect()->route('announcements.index')
                         ->with('success', 'Pengumuman berhasil dihapus.');
    }
}
