@extends('backend.layouts.master')

@section('title', 'Create Product')

@section('main-content')
    <div class="card">
        <h5 class="card-header">Create Product</h5>
        <div class="card-body">
            <form method="POST" action="{{ route('product.store') }}" enctype="multipart/form-data">
                @csrf
                {{-- BASIC INFO --}}
                <div class="form-group">
                    <label for="title" class="col-form-label">Title <span class="text-danger">*</span></label>
                    <input id="title" type="text" name="title" class="form-control" value="{{ old('title') }}"
                        required>
                    @error('title')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="summary" class="col-form-label">Summary <span class="text-danger">*</span></label>
                    <textarea id="summary" name="summary" class="form-control" rows="3">{{ old('summary') }}</textarea>
                    @error('summary')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description" class="col-form-label">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="5">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                {{-- MAIN PHOTO --}}
                {{-- FOTO PRODUK (banyak) --}}
                <div class="form-group">
                    <label class="col-form-label">Foto Produk <span class="text-danger">*</span></label>

                    <div id="image-wrapper" class="d-flex flex-wrap" style="gap:8px;">
                        {{-- preview foto akan di-append di sini --}}
                        <button type="button" id="btn-add-image" class="btn btn-light border"
                            style="width:100px;height:100px;display:flex;align-items:center;justify-content:center;flex-direction:column;">
                            <span style="font-size:24px;">+</span>
                            <small>Tambahkan Foto</small>
                        </button>
                    </div>

                    <input type="file" id="input-images" name="images[]" class="d-none" accept="image/*" multiple>

                    <input type="hidden" name="orders" id="orders">

                    @error('images')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    @error('images.*')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                {{-- VIDEO PRODUK (hanya 1) --}}
                <div class="form-group">
                    <label class="col-form-label">Video Produk (opsional)</label>
                    <input type="file" name="video" accept="video/mp4,video/quicktime,video/x-msvideo"
                        class="form-control">
                    @error('video')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                {{-- CATEGORY --}}
                <div class="form-group">
                    <label for="cat_id" class="col-form-label">Category <span class="text-danger">*</span></label>
                    <select id="cat_id" name="cat_id" class="form-control" required>
                        <option value="">----Select Category----</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('cat_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->title }}</option>
                        @endforeach
                    </select>
                    @error('cat_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group {{ old('child_cat_id') ? '' : 'd-none' }}" id="child_cat_div">
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
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    </select>
                    @error('status')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                {{-- VARIANTS --}}
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Varian Produk</h5>
                        <button type="button" class="btn btn-sm btn-primary" id="btn-add-variant">+ Tambah Varian</button>
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
                                <tbody id="variants-body"><!-- rows via JS --></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-success">Submit</button>
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
        }

        .variant-photo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
        }

        .dragging {
            cursor: grabbing;
            transform: scale(.92);
            opacity: .8;
        }

        .drag-ghost {
            border: 2px dashed #0d6efd;
            background: rgba(13, 110, 253, .05);
        }

        .sortable-item {
            cursor: grab;
        }

        .sortable-item:active {
            cursor: grabbing;
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

        // CHILD CATEGORY AJAX
        $('#cat_id').on('change', function() {
            let id = $(this).val();
            if (!id) {
                $('#child_cat_div').addClass('d-none');
                $('#child_cat_id').html("<option value=''>----Select sub category----</option>");
                return;
            }
            $.post("/admin/category/" + id + "/child", {
                _token: "{{ csrf_token() }}",
                id: id
            }, function(resp) {
                if (typeof resp !== 'object') resp = $.parseJSON(resp);
                let html = "<option value=''>----Select sub category----</option>";
                if (resp.status) {
                    $('#child_cat_div').removeClass('d-none');
                    $.each(resp.data, (cid, title) => html += `<option value="${cid}">${title}</option>`);
                } else $('#child_cat_div').addClass('d-none');
                $('#child_cat_id').html(html);
            });
        });

        // VARIANTS (Add, Remove, Sort)
        const $tbody = document.getElementById('variants-body');
        const $btnAdd = document.getElementById('btn-add-variant');
        let variantSortCounter = 1;

        function rowHtml(id, sort) {
            const name = `variants[${id}]`;
            return `
                <tr data-row="${id}">
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
                <td><input type="number" min="1" name="${name}[sort_order]" class="form-control" value="${sort}"></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-danger btn-remove-variant">&times;</button></td>
                </tr>`;
        }

        function addRow() {
            $tbody.insertAdjacentHTML('beforeend', rowHtml(Date.now(), variantSortCounter++));
        }
        addRow();
        $btnAdd.addEventListener('click', addRow);
        $tbody.addEventListener('click', e => {
            if (e.target.classList.contains('btn-remove-variant')) e.target.closest('tr').remove();
        });
        $('#variants-body').on('change', 'input[name*="[sort_order]"]', function() {
            [...$tbody.children].sort((a, b) => a.querySelector('input[name*="[sort_order]"]').value - b
                    .querySelector('input[name*="[sort_order]"]').value)
                .forEach((row, i) => {
                    row.querySelector('input[name*="[sort_order]"]').value = i + 1;
                    $tbody.append(row);
                });
        });

        // FOTO PRODUK (UPLOAD SATU PER SATU + PREVIEW + DRAG SORT)
        let filesList = [];

        const wrapper = document.getElementById('image-wrapper');
        const inputImages = document.getElementById('input-images');
        const btnAddImage = document.getElementById('btn-add-image');
        const orderField = document.getElementById('orders');

        btnAddImage.addEventListener('click', function() {
            inputImages.click();
        });

        inputImages.addEventListener('change', function(e) {
            const newFiles = Array.from(e.target.files);
            newFiles.forEach(file => {
                if (!file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = function(ev) {
                    const div = document.createElement('div');
                    div.className = 'sortable-item position-relative';
                    div.style.cssText = "width:100px;height:100px;cursor:grab;";
                    div.filePayload = file;
                    div.innerHTML = `
                    <img src="${ev.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
                    <button type="button" class="btn btn-sm btn-danger position-absolute btn-remove-new"
                    style="top:2px;right:2px;padding:0 4px;line-height:1;">×</button>
                    `;
                    // Event Hapus Gambar
                    div.querySelector(".btn-remove-new").addEventListener("click", function() {
                        div.remove();
                    });
                    wrapper.insertBefore(div, btnAddImage);
                };
                reader.readAsDataURL(file);
            });
            inputImages.value = "";
        });

        new Sortable(wrapper, {
            animation: 200,
            ghostClass: 'drag-ghost',
            dragClass: 'dragging',
            draggable: '.sortable-item',
        });

        /* saat submit → gabungkan file sesuai urutan */
        const productForm = document.querySelector('form[action*="product"]');
        productForm.addEventListener("submit", function(e) {
            const dataTransfer = new DataTransfer();
            const allPreviews = wrapper.querySelectorAll('.sortable-item');
            allPreviews.forEach(el => {
                if (el.filePayload) {
                    dataTransfer.items.add(el.filePayload);
                }
            });
            inputImages.files = dataTransfer.files;
            orderField.value = "";
        });
        // PREVIEW FOTO VARIANT
        $('#variants-body').on('change', '.variant-photo-input', function() {
            const prev = $(this).closest('tr').find('.variant-photo-preview')[0];
            const file = this.files[0];
            if (!file) return;
            const r = new FileReader();
            r.onload = e => prev.innerHTML =
                `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover">`;
            r.readAsDataURL(file);
        });
    </script>
@endpush
