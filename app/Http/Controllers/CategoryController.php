<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::getAllCategory();
        return view('backend.category.index', compact('categories'));
    }

    /**
     * Show the category creation form.
     */
    public function create()
    {
        $parent_cats = Category::where('is_parent', 1)->orderBy('title', 'ASC')->get();
        return view('backend.category.create', compact('parent_cats'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'summary' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'is_parent' => 'sometimes|in:1',
            'parent_id' => 'nullable|exists:categories,id',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Slug
        $validatedData['slug'] = generateUniqueSlug($request->title, Category::class);

        // Jika kategori ini parent, maka parent_id harus null
        $validatedData['is_parent'] = $request->input('is_parent', 0);
        if ($validatedData['is_parent'] == 1) {
            $validatedData['parent_id'] = null;
        }

        // Upload file photo
        if ($request->hasFile('photo')) {
            $validatedData['photo'] = $request->file('photo')->store('categories', 'public');
        }

        // Create category
        $category = Category::create($validatedData);

        return redirect()->route('category.index')
            ->with($category ? 'success' : 'error', $category ? 'Category successfully added' : 'Error occurred!');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $parent_cats = Category::where('is_parent', 1)->get();
        return view('backend.category.edit', compact('category', 'parent_cats'));
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validatedData = $request->validate([
            'title' => 'required|string',
            'summary' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'is_parent' => 'sometimes|in:1',
            'parent_id' => 'nullable|exists:categories,id',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Jika menjadi parent, maka parent_id harus null
        $validatedData['is_parent'] = $request->input('is_parent', 0);
        if ($validatedData['is_parent'] == 1) {
            $validatedData['parent_id'] = null;
        }

        // Slug bisa ikut berubah (opsional)
        $validatedData['slug'] = generateUniqueSlug($request->title, Category::class, $id);

        // Jika upload foto baru
        if ($request->hasFile('photo')) {

            // Hapus foto lama
            if ($category->photo && Storage::disk('public')->exists($category->photo)) {
                Storage::disk('public')->delete($category->photo);
            }

            // Upload baru
            $validatedData['photo'] = $request->file('photo')->store('categories', 'public');
        }

        $category->update($validatedData);

        return redirect()->route('category.index')
            ->with('success', 'Category successfully updated');
    }

    /**
     * Remove the specified category.
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // --- TAMBAHAN VALIDASI PENCEGAHAN ---
        // Cek apakah ada produk yang masih memakai kategori atau sub-kategori ini
        $productCount = \App\Models\Product::where('cat_id', $id)
                                           ->orWhere('child_cat_id', $id)
                                           ->count();

        if ($productCount > 0) {
            return redirect()->route('category.index')
                ->with('error', 'Gagal! Kategori ini tidak bisa dihapus karena masih digunakan oleh ' . $productCount . ' produk. Pindahkan atau hapus produknya terlebih dahulu.');
        }
        // --- AKHIR TAMBAHAN VALIDASI ---

        // Ambil child category
        $child_cat_id = Category::where('parent_id', $id)->pluck('id');

        // Hapus foto dari storage
        if ($category->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($category->photo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($category->photo);
        }

        // Hapus data kategori
        $status = $category->delete();

        // Jika ada child, pindahkan (shift)
        if ($status && $child_cat_id->count() > 0) {
            Category::shiftChild($child_cat_id);
        }

        $message = $status
            ? 'Category successfully deleted'
            : 'Error while deleting category';

        return redirect()->route('category.index')
            ->with($status ? 'success' : 'error', $message);
    }

    /**
     * Get child categories.
     */
    public function getChildByParent(Request $request)
    {
        $category = Category::findOrFail($request->id);
        $child_cat = Category::getChildByParentID($request->id);

        if ($child_cat->count() <= 0) {
            return response()->json(['status' => false, 'msg' => '', 'data' => null]);
        }

        return response()->json(['status' => true, 'msg' => '', 'data' => $child_cat]);
    }
// ini untuk list order
    public function updateOrder(Request $request)
    {
    if ($request->has('home_display_limit')) {
            Cache::forever('home_category_limit', $request->home_display_limit);
    }
    if($request->has('position')){
        foreach($request->position as $id => $pos){
            \App\Models\Category::where('id', $id)->update(['position' => $pos]);
        }
        return redirect()->back()->with('success', 'Urutan kategori berhasil diperbarui');
    }
    return redirect()->back()->with('error', 'Gagal memperbarui urutan');
}
}
