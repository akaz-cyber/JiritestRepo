<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::latest('id')->paginate(10);
        return view('backend.banner.index', compact('banners'));
    }

    public function create()
    {
        return view('backend.banner.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'link' => 'required|url',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('photo')) {
            $validatedData['photo'] = $request->file('photo')->store('banners', 'public');
        }

        $banner = Banner::create($validatedData);

        return redirect()->route('banner.index')->with(
            $banner ? 'success' : 'error',
            $banner ? 'Banner successfully added' : 'Error occurred while adding banner'
        );
    }


    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        return view('backend.banner.edit', compact('banner'));
    }


    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $validatedData = $request->validate([
            'link' => 'required|url',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('photo')) {

            // Hapus foto lama
            if ($banner->photo && Storage::disk('public')->exists($banner->photo)) {
                Storage::disk('public')->delete($banner->photo);
            }

            $validatedData['photo'] = $request->file('photo')->store('banners', 'public');
        }

        $banner->update($validatedData);

        return redirect()->route('banner.index')->with(
            'success',
            'Banner successfully updated'
        );
    }


    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        // Hapus foto dari storage
        if ($banner->photo && Storage::disk('public')->exists($banner->photo)) {
            Storage::disk('public')->delete($banner->photo);
        }

        $status = $banner->delete();

        return redirect()->route('banner.index')->with(
            $status ? 'success' : 'error',
            $status ? 'Banner successfully deleted' : 'Error occurred while deleting banner'
        );
    }
}
