<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Illuminate\Support\Facades\Log; //nambah ini yang baru untuk debuging
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\ProductView;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    /**
     * List produk (backend) + pencarian & show entries.
     */
    public function index(Request $request)
    {
        $q       = trim($request->input('q', ''));
        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10,25,50,100])) $perPage = 10;

        $query = Product::with([
            'variants:id,product_id,sku,variant_name,sort_order,is_active,price,discount_percent,stock',
            'cat_info:id,title', 'sub_cat_info:id,title',
        ]);

        if ($q !== '') {
            $query->where(function($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('slug', 'like', "%{$q}%")
                  ->orWhereHas('variants', function($v) use ($q) {
                      $v->where('sku', 'like', "%{$q}%")
                        ->orWhere('variant_name', 'like', "%{$q}%");
                  });
            });
        }

        // $products = $query->orderByDesc('id')->get();

        $products = $query->orderByDesc('id')
                          ->paginate($perPage)
                          ->appends($request->only('q','per_page'));

        return view('backend.product.index', compact('products','q','perPage'));
    }

    /**
     * Form create (backend)
     */
    public function create()
    {
        $categories = Category::where('is_parent', 1)->get();
        return view('backend.product.create', compact('categories'));
    }

    /**
     * Simpan produk + varian (backend)
     */
    public function store(Request $request)
    {
        $variantCount = count($request->input('variants', []));

        $validated = $request->validate([
            'title'        => 'required|string|unique:products,title',
            'summary'      => 'required|string',
            'description'  => 'nullable|string',

            // ganti LFM → upload file
            'images'       => 'required|array',
            'images.*'     => 'image|mimes:jpeg,png,jpg,webp|max:2048',

            // video satu file (opsional)
            'video'        => 'nullable|mimetypes:video/mp4,video/quicktime,video/x-msvideo|max:102400',

            'cat_id'       => 'required|exists:categories,id',
            'child_cat_id' => 'nullable|exists:categories,id',
            'status'       => 'required|in:active,inactive',

            // varian (opsional; jika ada baris diisi maka price wajib)
            'variants'                      => 'nullable|array',
            'variants.*.variant_name'       => 'required_with:variants.*.price|string',
            'variants.*.price'              => 'required_with:variants.*.variant_name|numeric|min:0',
            'variants.*.original_price'     => 'nullable|numeric|min:0',
            'variants.*.discount_percent'   => 'nullable|integer|min:0|max:99',
            'variants.*.stock'              => 'nullable|integer|min:0',
            'variants.*.weight'             => 'required|numeric|min:0', // gram
            'variants.*.sku'                => 'nullable|string|max:100|unique:product_variants,sku', //nambah produk variant pada sku biar ada requred
            'variants.*.is_active'          => 'nullable|boolean',
            "variants.*.sort_order"         => "required|integer|min:1|max:{$variantCount}",
            'variants.*.photo'              => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            // 2. TAMBAHKAN ARRAY PESAN KUSTOM DI SINI
            'title.unique' => 'Maaf, judul produk ini sudah digunakan.',
            'variants.*.sku.unique' => 'maaf SKU pada varian sudah tersedia',
        ]);

        // field ini bukan kolom di tabel products
        unset($validated['images']);

        // helper slug milikmu
        $validated['slug'] = generateUniqueSlug($request->title, Product::class);

        // Simpan foto-foto ke storage
        $photoPaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $imageFile) {
                $photoPaths[$i] = $imageFile->store('products/images', 'public');
            }
        }

        // Urutkan berdasarkan drag & drop
        $orders = json_decode($request->orders ?? "[]", true);

        $finalPhotos = [];
        foreach ($orders as $item) {
            if ($item['type'] === 'old') {
                $finalPhotos[] = $item['value']; // path foto lama
            }
        }

        $existingPhotos = $finalPhotos;


        // jaga-jaga kalau somehow kosong setelah sorting
        if (empty($photoPaths)) {
            return back()
                ->withErrors(['images' => 'Foto produk tidak boleh kosong.'])
                ->withInput();
        }

        // simpan ke kolom `photo` (pakai | seperti sebelumnya)
        $validated['photo'] = implode('|', $photoPaths);

        // Simpan video (opsional, satu file)
        // dd($request->all(), $request->hasFile('video'), $request->file('video'));
        if ($request->hasFile('video')) {
            $validated['video'] = $request->file('video')->store('products/videos', 'public');
        }

        try {
            DB::beginTransaction();

            $product = Product::create($validated);

            // SIMPAN VARIAN (kode lama dimodif dikit)
        foreach (($request->input('variants') ?? []) as $key => $v) {
            if (empty($v['variant_name']) && empty($v['price'])) {
                continue;
            }

            $photoPath = null;
            if ($request->hasFile("variants.$key.photo")) {
                $photoPath = $request->file("variants.$key.photo")
                                    ->store('product_variants/images', 'public');
            }

            $product->variants()->create([
                'variant_name'     => $v['variant_name'] ?? '',
                'price'            => $v['price'] ?? 0,
                'original_price'   => $v['original_price'] ?? null,
                'discount_percent' => $v['discount_percent'] ?? null,
                'stock'            => $v['stock'] ?? 0,
                'weight'           => $v['weight'] ?? 0,
                'sku'              => $v['sku'] ?? null,
                'is_active'        => $v['is_active'] ?? 1,
                'sort_order'       => $v['sort_order'] ?? 1,
                'photo'            => $photoPath,
            ]);
        }


            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            //nambah log buat ke 2 ini
            Log::error('Error saat membuat produk baru: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                // 'trace' => $e->getTraceAsString() // Opsional: Buka komentar ini jika butuh trace yang sangat detail
            ]);


            return back()
                ->withInput()
                ->with('error', 'Error: '.$e->getMessage());
        }

        return redirect()
            ->route('product.index')
            ->with('success', 'Product successfully added');
    }


    /**
     * Form edit (backend)
     */
  public function edit($id)
    {
        $product = Product::with(['variants' => function($q) {
            $q->orderBy('sort_order');
        }])->findOrFail($id);

        $categories = Category::where('is_parent', 1)->get();

        return view('backend.product.edit', compact('product', 'categories'));
    }

    /**
     * Update produk + sinkronisasi varian (backend)
     */
public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    $variantCount = count($request->input('variants', []));

    $validated = $request->validate([
        'title'        => 'required|string',
        'summary'      => 'required|string',
        'description'  => 'nullable|string',

        'images'       => 'nullable|array',
        'images.*'     => 'image|mimes:jpeg,png,jpg,webp|max:2048',

        'video'        => 'nullable|mimetypes:video/mp4,video/quicktime,video/x-msvideo|max:102400',

        'cat_id'       => 'required|exists:categories,id',
        'child_cat_id' => 'nullable|exists:categories,id',
        'status'       => 'required|in:active,inactive',

        'variants'                      => 'nullable|array',
        'variants.*.id'                 => 'nullable|integer|exists:product_variants,id',
        'variants.*.variant_name'       => 'required_with:variants.*.price|string',
        'variants.*.price'              => 'required_with:variants.*.variant_name|numeric|min:0',
        'variants.*.original_price'     => 'nullable|numeric|min:0',
        'variants.*.discount_percent'   => 'nullable|integer|min:0|max:99',
        'variants.*.stock'              => 'nullable|integer|min:0',
        'variants.*.weight'             => 'required|numeric|min:0',
        'variants.*.sku'                => 'nullable|string|max:100',
        'variants.*.is_active'          => 'nullable|boolean',
        "variants.*.sort_order"         => "required|integer|min:1|max:{$variantCount}",
        'variants.*.photo'              => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    try {
        DB::beginTransaction();

        /* ============================================================
         |                     FOTO PRODUK (FINAL)
         ============================================================ */
        unset($validated['images']);

        // foto lama dari DB
        $existingPhotos = array_filter(explode('|', $product->photo ?? ''));

        // list foto yang dihapus (JSON)
        $remove = json_decode($request->remove_images ?? "[]", true);
        if (!empty($remove)) {
            $existingPhotos = array_values(array_diff($existingPhotos, $remove));
            foreach ($remove as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        // foto baru yang diupload
        $newPhotos = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $newPhotos[] = $file->store('products/images', 'public');
            }
        }

        // susun ulang berdasarkan orders JSON
        $finalPhotos = [];
        $orders = json_decode($request->orders ?? "[]", true);
        if (empty($orders)) {
            // tidak drag — gabungkan foto lama + foto baru
            $validated['photo'] = implode('|', array_merge($existingPhotos, $newPhotos));
        } else {
            // drag — susun berdasarkan urutan JSON
            $finalPhotos = [];
            foreach ($orders as $item) {
                if ($item['type'] === 'old') $finalPhotos[] = $item['value'];
                if ($item['type'] === 'new') $finalPhotos[] = $newPhotos[$item['value']] ?? null;
            }
            $finalPhotos = array_values(array_filter($finalPhotos));
            $validated['photo'] = implode('|', $finalPhotos);
        };

        /* ============================================================
         |                          VIDEO
         ============================================================ */
        $currentVideo = $product->video;

        if ($request->hasFile('video')) {
            if ($currentVideo) Storage::disk('public')->delete($currentVideo);
            $currentVideo = $request->file('video')->store('products/videos', 'public');
        }

        if ($request->boolean('remove_video')) {
            if ($currentVideo) Storage::disk('public')->delete($currentVideo);
            $currentVideo = null;
        }

        $validated['video'] = $currentVideo;


        /* ============================================================
         |                        SIMPAN PRODUCT
         ============================================================ */
        $product->update($validated);


        /* ============================================================
         |               SYNC VARIANTS (UPDATE & CREATE & DELETE)
         ============================================================ */
        $incoming     = $request->input('variants', []);
        $existingIds  = $product->variants()->pluck('id')->all();
        $submittedIds = array_values(array_filter(array_map(fn($x) => $x['id'] ?? null, $incoming)));

        $toDelete = array_diff($existingIds, $submittedIds);
        $product->variants()->whereIn('id', $toDelete)->delete();

        foreach ($incoming as $key => $v) {
            if (empty($v['variant_name']) && empty($v['price'])) continue;

            $dataVariant = [
                'variant_name'     => $v['variant_name'] ?? '',
                'price'            => $v['price'] ?? 0,
                'original_price'   => $v['original_price'] ?? null,
                'discount_percent' => $v['discount_percent'] ?? null,
                'stock'            => $v['stock'] ?? 0,
                'weight'           => $v['weight'] ?? 0,
                'sku'              => $v['sku'] ?? null,
                'is_active'        => !empty($v['is_active']) ? 1 : 0,
                'sort_order'       => $v['sort_order'] ?? 1,
            ];

            $fileKey = "variants.$key.photo";

            // update varian lama
            if (!empty($v['id'])) {
                $variant = $product->variants()->where('id', $v['id'])->first();
                if (!$variant) continue;

                $photoPath = $variant->photo;

                if ($request->hasFile($fileKey)) {
                    if ($photoPath) Storage::disk('public')->delete($photoPath);
                    $photoPath = $request->file($fileKey)
                                         ->store('product_variants/images', 'public');
                }

                $dataVariant['photo'] = $photoPath;
                $variant->update($dataVariant);
            }
            // varian baru
            else {
                $photoPath = null;
                if ($request->hasFile($fileKey)) {
                    $photoPath = $request->file($fileKey)
                                         ->store('product_variants/images', 'public');
                }
                $dataVariant['photo'] = $photoPath;

                $product->variants()->create($dataVariant);
            }
        }

        DB::commit();
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Error saat update produk (ID: ' . $id . '): ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        return back()->withInput()->with('error', 'Please try again!');
    }

    return redirect()->route('product.index')->with('success', 'Product successfully updated');
}



    /**
     * Hapus produk (backend)
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $status  = $product->delete();

        return redirect()->route('product.index')
            ->with($status ? 'success' : 'error', $status ? 'Product successfully deleted' : 'Error while deleting product');
    }

    /**
     * Detail produk untuk frontend di pindah kan kesini
     *
     */
    public function detail(string $slug)
    {
        $product_detail = Product::with([
            'variants'     => fn($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'cat_info',
            'sub_cat_info',
            'rel_prods',
            'getReview',
        ])->where('slug', $slug)->firstOrFail();

        $currentVariantIds = $product_detail->variants->pluck('id')->toArray();
        $recommendedVariantIds = \Illuminate\Support\Facades\DB::table('association_rules')
            ->whereIn('antecedent_variant_id', $currentVariantIds)
            ->orderByDesc('confidence')
            ->limit(8)
            ->pluck('consequent_variant_id')
            ->toArray();
        $recommendedProducts = collect();
       $recommendedVariants = collect();
        if (!empty($recommendedVariantIds)) {
            $recommendedVariants = \App\Models\ProductVariant::with('product')
                ->whereIn('id', $recommendedVariantIds)
                ->whereHas('product', function($q) use ($product_detail) {
                    $q->where('id', '!=', $product_detail->id)
                      ->where('status', 'active');
                })
                ->get();
        }

        ProductView::create(['product_id' => $product_detail->id]);
        return view('frontend.pages.product_detail', compact('product_detail', 'recommendedVariants'));
    }
}
