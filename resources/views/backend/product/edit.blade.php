@extends('backend.layouts.master')

@section('title', 'Edit Product')

@section('main-content')
<div class="card">
    <h5 class="card-header">Edit Product</h5>
    <div class="card-body">
        <form method="POST" action="{{ route('product.update', $product->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- BASIC INFO --}}
            <div class="form-group">
                <label for="title" class="col-form-label">Title <span class="text-danger">*</span></label>
                <input id="title" type="text" name="title" class="form-control"
                       value="{{ old('title', $product->title) }}" required>
                @error('title') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="summary" class="col-form-label">Summary <span class="text-danger">*</span></label>
                <textarea id="summary" name="summary" class="form-control" rows="3">{{ old('summary', $product->summary) }}</textarea>
                @error('summary') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="description" class="col-form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="5">{{ old('description', $product->description) }}</textarea>
                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            {{-- FOTO PRODUK (LAMA & BARU) --}}
            <div class="form-group">
                <label class="col-form-label">Foto Produk</label>

                <div id="image-all-wrapper" class="d-flex flex-wrap" style="gap:8px;">

                    {{-- Foto lama --}}
                    @foreach ($product->photos_array as $img)
                        <div class="sortable-item old-photo position-relative"
                            data-type="old"
                            data-path="{{ $img }}"
                            style="width:100px;height:100px;cursor:grab;">
                            <img src="{{ asset('storage/' . $img) }}"
                                style="width:100%;height:100%;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
                            <button type="button"
                                class="btn btn-sm btn-danger position-absolute btn-remove-photo"
                                style="top:2px;right:2px;padding:0 4px;">×</button>
                        </div>
                    @endforeach

                    {{-- Tombol tambah --}}
                    <button type="button" id="btn-add-image" class="btn btn-light border"
                        style="width:100px;height:100px;display:flex;align-items:center;justify-content:center;flex-direction:column;">
                        <span style="font-size:24px;">+</span>
                        <small>Tambahkan Foto</small>
                    </button>
                </div>

                <input type="file" id="input-images" name="images[]" class="d-none" accept="image/*" multiple>

                {{-- Kirim urutan baru (gabungan foto lama & foto baru) --}}
                <input type="hidden" name="orders" id="orders">
                {{-- Kirim file mana saja foto lama yang dihapus --}}
                <input type="hidden" name="remove_images" id="remove_images">

                @error('images') <span class="text-danger">{{ $message }}</span> @enderror
                @error('images.*') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            {{-- VIDEO --}}
            <div class="form-group mt-3">
                <label class="col-form-label">Video Produk (opsional)</label>

                @if ($product->video)
                    <div class="mb-2">
                        <video src="{{ asset('storage/' . $product->video) }}" width="240" controls></video>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" name="remove_video" value="1" class="form-check-input" id="removeVideo">
                        <label class="form-check-label" for="removeVideo">Hapus video</label>
                    </div>
                @endif

                <input type="file" name="video" class="form-control" accept="video/mp4,video/*">
                <small class="form-text text-muted">Jika upload baru, video lama akan diganti.</small>
                @error('video') <span class="text-danger">{{ $message }}</span> @enderror
            </div>


                {{-- CATEGORY --}}
                <div class="form-group">
                    <label for="cat_id" class="col-form-label">Category <span class="text-danger">*</span></label>
                    <select id="cat_id" name="cat_id" class="form-control" required>
                        <option value="">----Select Category----</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('cat_id', $product->cat_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('cat_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group {{ $product->child_cat_id ? '' : 'd-none' }}" id="child_cat_div">
                    <label for="child_cat_id" class="col-form-label">Sub Category</label>
                    <select id="child_cat_id" name="child_cat_id" class="form-control">
                        <option value="">----Select sub category----</option>
                    </select>
                    @error('child_cat_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                {{-- STATUS --}}
                <div class="form-group">
                    <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>
                            Inactive</option>
                        <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>
                            Active</option>
                    </select>
                    @error('status')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                {{-- VARIANTS --}}
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Varian Produk</h5>
                        <button type="button" class="btn btn-sm btn-primary" id="btn-add-variant">+ Tambah
                            Varian</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" id="variants-table">
                                <thead>
                                    <tr>
                                        <th style="min-width:160px;">Variant Name</th>
                                        <th style="min-width:120px;">Harga Jual</th>
                                        <th style="min-width:130px;">Diskon (%)</th>
                                        <th style="min-width:120px;">Harga Asli</th>
                                        <th style="min-width:100px;">Stock</th>
                                        <th style="min-width:120px;">Weight (gram)</th>
                                        <th style="min-width:120px;">SKU</th>
                                        <th style="min-width:160px;">Foto</th>
                                        <th style="min-width:110px;">Active</th>
                                        <th style="min-width:90px;">Sort</th>
                                        <th style="min-width:80px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="variants-body">
                                    @foreach ($product->variants->sortBy('sort_order') as $v)
                                        @php $rowId = 'exist_'.$v->id; @endphp
                                        <tr data-row="{{ $rowId }}">
                                            <td>
                                                <input type="hidden" name="variants[{{ $rowId }}][id]"
                                                    value="{{ $v->id }}">
                                                <input type="text" name="variants[{{ $rowId }}][variant_name]"
                                                    class="form-control"
                                                    value="{{ old('variants.' . $rowId . '.variant_name', $v->variant_name) }}"
                                                    required>
                                                @error('variants.' . $rowId . '.variant_name')
                                                    <span class="text-danger d-block mt-1">{{ $message }}</span>
                                                @enderror
                                            </td>

                                            {{-- ======== PERBAIKAN DI SINI ======== --}}
                                            <td>
                                                <input type="number" step="0.01" min="0"
                                                    name="variants[{{ $rowId }}][price]" class="form-control"
                                                    value="{{ old('variants.' . $rowId . '.price', $v->price) }}">

                                                {{-- TAMBAHKAN BLOK ERROR INI --}}
                                                @error('variants.' . $rowId . '.price')
                                                    <span class="text-danger d-block mt-1"
                                                        style="font-size: 85%;">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            {{-- ======== AKHIR PERBAIKAN ======== --}}

                                            <td>
                                                <input type="number" min="0" max="99"
                                                    name="variants[{{ $rowId }}][discount_percent]"
                                                    class="form-control"
                                                    value="{{ old('variants.' . $rowId . '.discount_percent', $v->discount_percent) }}">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0"
                                                    name="variants[{{ $rowId }}][original_price]"
                                                    class="form-control"
                                                    value="{{ old('variants.' . $rowId . '.original_price', $v->original_price) }}">
                                            </td>
                                            <td>
                                                <input type="number" min="0"
                                                    name="variants[{{ $rowId }}][stock]" class="form-control"
                                                    value="{{ old('variants.' . $rowId . '.stock', $v->stock) }}">
                                            </td>

                                            <td>
                                                <input type="number" step="0.01" min="0"
                                                    name="variants[{{ $rowId }}][weight]" class="form-control"
                                                    value="{{ old('variants.' . $rowId . '.weight', $v->weight) }}"
                                                    placeholder="0" required>
                                                @error('variants.' . $rowId . '.weight')
                                                    <span class="text-danger d-block mt-1"
                                                        style="font-size: 85%;">{{ $message }}</span>
                                                @enderror
                                            </td>

                                            <td>
                                                <input type="text" name="variants[{{ $rowId }}][sku]"
                                                    class="form-control"
                                                    value="{{ old('variants.' . $rowId . '.sku', $v->sku) }}">
                                            </td>

                                            @php
                                                $photoInputId = "v_photo_$rowId";
                                                $previewId = "v_preview_$rowId";
                                            @endphp
                                            <td>
                                                <div class="d-flex align-items-center" style="gap:8px;">
                                                    <div id="{{ $previewId }}" class="variant-photo-preview">
                                                        @if ($v->photo)
                                                            <img src="{{ asset('storage/' . $v->photo) }}"
                                                                alt="preview">
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <input id="{{ $photoInputId }}" type="file"
                                                            name="variants[{{ $rowId }}][photo]"
                                                            class="form-control-file variant-photo-input"
                                                            accept="image/*">
                                                        <small class="text-muted d-block">Kosongkan jika tidak ingin
                                                            mengganti.</small>
                                                    </div>
                                                </div>
                                            </td>


                                            <td class="text-center">
                                                <input type="checkbox" name="variants[{{ $rowId }}][is_active]"
                                                    value="1"
                                                    {{ old('variants.' . $rowId . '.is_active', $v->is_active) ? 'checked' : '' }}>
                                            </td>
                                            <td>
                                                <input type="number" min="1"
                                                    name="variants[{{ $rowId }}][sort_order]" class="form-control"
                                                    value="{{ old('variants.' . $rowId . '.sort_order', $v->sort_order) }}">
                                                @error('variants.' . $rowId . '.sort_order')
                                                    <span class="text-danger d-block mt-1"
                                                        style="font-size: 85%;">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td class="text-center">
                                                <button type="button"
                                                    class="btn btn-sm btn-danger btn-remove-variant">&times;</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="{{ route('product.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/summernote/summernote.min.css') }}">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
    <style>
        .variant-photo-preview {
            display: inline-block;
            width: 60px;
            height: 60px;
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }

        .variant-photo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .dragging {
            cursor: grabbing;
            transform: scale(.92);
            opacity: .8;
        }
        .drag-ghost {
            border: 2px dashed #0d6efd;
            background: rgba(13,110,253,.05);
        }

    </style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script src="{{ asset('backend/summernote/summernote.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>

<script>
// SUMMERNOTE 
$('#summary').summernote({
    placeholder: 'Write short description.....',
    height: 100
});
$('#description').summernote({
    placeholder: 'Write detail description.....',
    height: 150
});

// CHILD CATEGORY AJAX (with initial)
function loadChildCategories(cat_id, initialChildId = null) {
    if (!cat_id) {
        $('#child_cat_div').addClass('d-none');
        $('#child_cat_id').html("<option value=''>----Select sub category----</option>");
        return;
    }
    $.ajax({
        url: "/admin/category/" + cat_id + "/child",
        type: "POST",
        data: { _token: "{{ csrf_token() }}", id: cat_id },
        success: function(resp) {
            if (typeof resp !== 'object') resp = $.parseJSON(resp);
            let html = "<option value=''>----Select sub category----</option>";
            if (resp.status && resp.data) {
                $('#child_cat_div').removeClass('d-none');
                $.each(resp.data, function(id, title) {
                    const selected = (initialChildId == id) ? 'selected' : '';
                    html += `<option value="${id}" ${selected}>${title}</option>`;
                });
            } else {
                $('#child_cat_div').addClass('d-none');
            }
            $('#child_cat_id').html(html);
        }
    });
}

const initialCatId    = '{{ old('cat_id', $product->cat_id) }}';
const initialChildCatId = '{{ old('child_cat_id', $product->child_cat_id) }}';

if (initialCatId) {
    loadChildCategories(initialCatId, initialChildCatId);
}

$('#cat_id').on('change', function() {
    loadChildCategories($(this).val(), null);
});

//  VARIANTS (Add, Remove, Sort 
const $tbody = document.getElementById('variants-body');
const $btnAdd = document.getElementById('btn-add-variant');

// Hitung sort awal dari varian yang sudah ada
let variantSortCounter = 1;
const existingSortInputs = $tbody.querySelectorAll('input[name*="[sort_order]"]');
if (existingSortInputs.length > 0) {
    const maxSort = Math.max(
        ...Array.from(existingSortInputs).map(i => parseInt(i.value || '0', 10))
    );
    variantSortCounter = (isFinite(maxSort) ? maxSort : 0) + 1;
}

function rowHtml(rowId, sortOrder) {
    const name = `variants[${rowId}]`;
    return `
<tr data-row="${rowId}">
  <td><input type="text" name="${name}[variant_name]" class="form-control" placeholder="cth: 1 Kg"></td>
  <td><input type="number" step="0.01" min="0" name="${name}[price]" class="form-control" placeholder="0" required></td>
  <td><input type="number" min="0" max="99" name="${name}[discount_percent]" class="form-control" placeholder="0"></td>
  <td><input type="number" step="0.01" min="0" name="${name}[original_price]" class="form-control" placeholder="0"></td>
  <td><input type="number" min="0" name="${name}[stock]" class="form-control" placeholder="0"></td>
  <td><input type="number" step="0.01" min="0" name="${name}[weight]" class="form-control" placeholder="0" required></td>
  <td><input type="text" name="${name}[sku]" class="form-control" placeholder="SKU"></td>
  <td>
    <div class="d-flex align-items-center" style="gap:8px;">
        <div class="variant-photo-preview"></div>
        <input type="file" name="${name}[photo]" class="form-control-file variant-photo-input" accept="image/*">
    </div>
  </td>
  <td class="text-center"><input type="checkbox" name="${name}[is_active]" value="1" checked></td>
  <td><input type="number" min="1" name="${name}[sort_order]" class="form-control" value="${sortOrder}"></td>
  <td class="text-center"><button type="button" class="btn btn-sm btn-danger btn-remove-variant">&times;</button></td>
</tr>`;
}

// tambah baris varian baru
$btnAdd.addEventListener('click', function() {
    const id = Date.now();
    $tbody.insertAdjacentHTML('beforeend', rowHtml(id, variantSortCounter++));
});

// hapus baris varian
$tbody.addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-remove-variant')) {
        e.target.closest('tr').remove();
    }
});

// sort varian berdasarkan kolom sort_order
$('#variants-body').on('change', 'input[name*="[sort_order]"]', function() {
    const rows = Array.from($tbody.querySelectorAll('tr'));
    rows.sort((a, b) => {
        const sa = parseInt(a.querySelector('input[name*="[sort_order]"]').value || '0', 10);
        const sb = parseInt(b.querySelector('input[name*="[sort_order]"]').value || '0', 10);
        return sa - sb;
    }).forEach((row, i) => {
        row.querySelector('input[name*="[sort_order]"]').value = i + 1;
        $tbody.appendChild(row);
    });
});

// preview foto varian (baru & lama)
$('#variants-body').on('change', '.variant-photo-input', function() {
    const preview = $(this).closest('tr').find('.variant-photo-preview')[0];
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => preview.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover">`;
    reader.readAsDataURL(file);
});

const oldWrapper  = document.getElementById('old-image-wrapper');
const ordersField = document.getElementById('orders');

// drag & drop urutan foto lama
if (oldWrapper) {
    new Sortable(oldWrapper, {
        animation: 200,
        ghostClass: 'drag-ghost',
        dragClass: 'dragging',
        onSort() {
            const order = [...oldWrapper.querySelectorAll('.sortable-old')]
                .map(el => el.dataset.index);
            ordersField.value = order.join(',');
        }
    });
}

// hapus foto lama → kirim remove_images[]
document.querySelectorAll('.btn-remove-old').forEach(btn => {
    btn.addEventListener('click', function() {
        const path = this.dataset.path;
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'remove_images[]';
        input.value = path;
        document.forms[0].appendChild(input);
        this.parentElement.remove();

        // update orders setelah salah satu foto lama dihapus
        if (oldWrapper) {
            const order = [...oldWrapper.querySelectorAll('.sortable-old')]
                .map(el => el.dataset.index);
            ordersField.value = order.join(',');
        }
    });
});

/* FOTO LAMA + BARU DALAM 1 WRAPPER */
const wrapper = document.getElementById('image-all-wrapper');
const inputAdd = document.getElementById('input-images');
const btnAdd = document.getElementById('btn-add-image');
const ordersInput = document.getElementById('orders');
const removedInput = document.getElementById('remove_images');
let removedList = [];
let newFiles = [];

// Tambah foto baru
btnAdd.addEventListener('click', () => inputAdd.click());

inputAdd.addEventListener('change', (e) => {
    [...e.target.files].forEach((file) => {
        newFiles.push(file);
        const reader = new FileReader();
        reader.onload = ev => {
            const div = document.createElement('div');
            div.className = 'sortable-item new-photo position-relative';
            div.dataset.type = "new";
            div.dataset.index = newFiles.length - 1;
            div.style.cssText = "width:100px;height:100px;cursor:grab;";
            div.innerHTML = `
                <img src="${ev.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
                <button type="button" class="btn btn-sm btn-danger position-absolute btn-remove-photo"
                    style="top:2px;right:2px;padding:0 4px;">×</button>
            `;
            wrapper.insertBefore(div, btnAdd);
        };
        reader.readAsDataURL(file);
    });
    inputAdd.value = "";
});

// Hapus foto lama ATAU baru
wrapper.addEventListener('click', function(e) {
    if (!e.target.classList.contains('btn-remove-photo')) return;
    const card = e.target.closest('.sortable-item');
    
    if (card.dataset.type === "old") {
        removedList.push(card.dataset.path);
        removedInput.value = JSON.stringify(removedList);
    }
    card.remove();
    updateOrders();
});

// Sortable
new Sortable(wrapper, {
    animation: 200,
    filter: "#btn-add-image",
    ghostClass: "drag-ghost",
    onSort: updateOrders
});

// susun urutan semua foto (lama & baru)
function updateOrders() {
    const order = [...wrapper.querySelectorAll('.sortable-item')]
        .filter(el => el.dataset.type) // skip tombol +
        .map(el => ({
            type: el.dataset.type,
            value: el.dataset.type === "old" ? el.dataset.path : parseInt(el.dataset.index)
        }));
    ordersInput.value = JSON.stringify(order);
}

// Saat submit → reorder newFiles berdasarkan urutan
const form = document.querySelector('form[action*="product"]');
form.addEventListener('submit', function() {
    const dt = new DataTransfer();
    const order = JSON.parse(ordersInput.value || "[]");

    if (!order.length) {
    newFiles.forEach(f => dt.items.add(f));
    } else {
        order.forEach(item => {
            if (item.type === "new") dt.items.add(newFiles[item.value]);
        });
    }
    inputAdd.files = dt.files;
});
</script>
@endpush