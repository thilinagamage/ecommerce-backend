@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title mb-4">Edit Product</h4>

                {{-- Display validation errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- BASIC INFO --}}
                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control" value="{{ old('slug', $product->slug) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Short Description</label>
                        <textarea name="short_description" class="form-control">{{ old('short_description', $product->short_description) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
                    </div>

                    {{-- IMAGES --}}
                    <div class="mb-3">
                        <label class="form-label">Featured Image</label>
                        @if ($product->featuredImage)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $product->featuredImage->path) }}" class="img-thumbnail"
                                    style="max-width:150px">
                            </div>
                        @endif
                        <input type="file" name="featured_image" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Product Gallery</label>
                        <input type="hidden" name="removed_gallery_images" id="removedGalleryImages">

                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($product->galleryImages as $image)
                                <div class="gallery-item position-relative" data-id="{{ $image->id }}" data-removed="0">
                                    <img src="{{ asset('storage/' . $image->path) }}" class="img-thumbnail"
                                        style="width:120px">
                                    <button type="button"
                                        class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-gallery-image">×</button>
                                </div>
                            @endforeach
                        </div>

                        <input type="file" name="gallery_images[]" class="form-control mt-2" multiple>
                    </div>

                    {{-- CATEGORIES --}}
                    <div class="mb-3">
                        <label class="form-label">Categories</label>
                        @foreach ($categories as $category)
                            <div>
                                <label>
                                    <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                        {{ $product->categories->contains($category->id) ? 'checked' : '' }}>
                                    {{ $category->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    {{-- TAGS --}}
                    <div class="mb-3">
                        <label class="form-label">Tags</label>
                        <select name="tags[]" multiple class="form-control">
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->id }}"
                                    {{ $product->tags->contains($tag->id) ? 'selected' : '' }}>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- PRODUCT TYPE --}}
                    <div class="mb-3">
                        <label class="form-label">Product Type</label>
                        <select name="product_type" id="product_type" class="form-select" required>
                            <option value="simple" {{ old('product_type', $product->type) == 'simple' ? 'selected' : '' }}>
                                Simple</option>
                            <option value="variable"
                                {{ old('product_type', $product->type) == 'variable' ? 'selected' : '' }}>Variable</option>
                        </select>
                    </div>

                    {{-- SIMPLE PRODUCT FIELDS --}}
                    <div id="simple-product-fields">

                        <h5 class="mt-4">Inventory</h5>
                        <div class="mb-3">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control"
                                value="{{ old('sku', $product->sku) }}">
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" name="manage_stock" value="1" class="form-check-input"
                                {{ old('manage_stock', $product->manage_stock) ? 'checked' : '' }} id="manage_stock">
                            <label class="form-check-label">Manage stock?</label>
                        </div>

                        <div id="stock-fields" style="{{ $product->manage_stock ? 'display:block' : 'display:none' }}">
                            <div class="mb-3">
                                <label>Stock Quantity</label>
                                <input type="number" name="stock_quantity" class="form-control"
                                    value="{{ old('stock_quantity', $product->stock_quantity) }}">
                            </div>
                            <div class="mb-3">
                                <label>Low Stock Threshold</label>
                                <input type="number" name="low_stock_threshold" class="form-control"
                                    value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}">
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" name="backorders" value="1" class="form-check-input"
                                    {{ old('backorders', $product->backorders) ? 'checked' : '' }}>
                                <label class="form-check-label">Allow backorders?</label>
                            </div>
                        </div>

                        <h5 class="mt-4">Pricing</h5>
                        <div class="mb-3">
                            <label class="form-label">Regular price</label>
                            <input type="number" step="0.01" name="regular_price" class="form-control"
                                value="{{ old('regular_price', $product->regular_price) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sale price</label>
                            <input type="number" step="0.01" name="sale_price" class="form-control"
                                value="{{ old('sale_price', $product->sale_price) }}">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Sale start date</label>
                                <input type="date" name="sale_price_from" class="form-control"
                                    value="{{ old('sale_price_from', $product->sale_price_from) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Sale end date</label>
                                <input type="date" name="sale_price_to" class="form-control"
                                    value="{{ old('sale_price_to', $product->sale_price_to) }}">
                            </div>
                        </div>

                    </div> {{-- END SIMPLE PRODUCT --}}

                    {{-- VARIABLE PRODUCT --}}
                    <div id="variable-attributes" style="display:{{ $product->type === 'variable' ? 'block' : 'none' }}">
                        <h5 class="mt-4">Attributes</h5>
                        @foreach ($attributes as $attribute)
                            <div class="mb-3">
                                <label>{{ $attribute->name }}</label>

                                <select class="form-control attribute-select" data-attribute-id="{{ $attribute->id }}"
                                    multiple>

                                    @foreach ($attribute->values as $value)
                                        <option value="{{ $value->id }}"
                                            {{ in_array($value->id, $selectedValueIds) ? 'selected' : '' }}>
                                            {{ $value->value }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                        @endforeach

                        <button type="button" id="generate-variations" class="btn btn-primary mb-3">Generate
                            Variations</button>

<table class="table" id="variation-table">
    <thead>
        <tr>
            <th>Variation</th>
            <th>SKU</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Image</th>
            <th>Remove</th>
        </tr>
    </thead>
    <tbody>
        @foreach($product->variations as $i => $variation)
            @php
                // Get unique attribute values only (shouldn't have duplicates, but just in case)
                $uniqueAttributes = $variation->attributes->unique('id');

                // Sort the attribute value IDs numerically before joining
                $comboKey = $uniqueAttributes
                    ->pluck('id')
                    ->sort()
                    ->values()
                    ->implode('-');

                // Create the label from attribute values
                $comboLabel = $uniqueAttributes
                    ->sortBy('product_attribute_id') // Sort by attribute ID for consistent display
                    ->map(fn($attr) => $attr->value)
                    ->implode(' / ');
            @endphp

            <tr data-id="{{ $variation->id }}"
                data-combo="{{ $comboKey }}"
                class="existing-variation">

                {{-- Hidden field to track variation ID --}}
                <input type="hidden" name="variations[{{ $i }}][id]" value="{{ $variation->id }}">

                {{-- Display combination label --}}
                <td class="variation-label">
                    {{ $comboLabel ?: 'No attributes' }}
                </td>

                {{-- Hidden inputs for variation attributes (ONE per unique attribute value) --}}
                @foreach($uniqueAttributes as $attr)
                    <input type="hidden"
                           name="variations[{{ $i }}][attributes][]"
                           value="{{ $attr->id }}">
                @endforeach

                {{-- SKU --}}
                <td>
                    <input type="text"
                           name="variations[{{ $i }}][sku]"
                           value="{{ $variation->sku }}"
                           class="form-control">
                </td>

                {{-- Price --}}
                <td>
                    <input type="number"
                           step="0.01"
                           name="variations[{{ $i }}][regular_price]"
                           value="{{ $variation->regular_price }}"
                           class="form-control"
                           required>
                </td>

                {{-- Stock --}}
                <td>
                    <input type="number"
                           name="variations[{{ $i }}][stock_quantity]"
                           value="{{ $variation->stock_quantity }}"
                           class="form-control">
                </td>

                {{-- Image --}}
                <td>
                    <input type="file"
                           name="variations[{{ $i }}][image]"
                           class="variation-image-input">

                    @if($variation->image)
                        <div class="position-relative d-inline-block mt-1">
                            <img src="{{ asset('storage/' . $variation->image) }}"
                                 class="variation-preview"
                                 style="max-width:60px;">
                            <button type="button"
                                    class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-variation-image"
                                    data-id="{{ $variation->id }}"
                                    style="padding: 0 4px; font-size: 12px;">
                                ×
                            </button>
                        </div>
                    @else
                        <img class="variation-preview mt-1" style="max-width:60px; display:none;">
                    @endif
                </td>

                {{-- Remove button --}}
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-variation">
                        Remove
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- Hidden inputs for tracking removals --}}
<input type="hidden" name="removed_variation_images" id="removedVariationImages">
<input type="hidden" name="removed_variations" id="removedVariations">


                    </div>

                    {{-- COLLECTION --}}
                    <div class="mb-3">
                        <label class="form-label">Collection</label>
                        <select name="collection_id" class="form-select">
                            <option value="">-- Select --</option>
                            @foreach ($collections as $collection)
                                <option value="{{ $collection->id }}"
                                    {{ old('collection_id', $product->collection_id) == $collection->id ? 'selected' : '' }}>
                                    {{ $collection->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- STATUS --}}
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-select">
                            <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>
                                Draft</option>
                            <option value="published"
                                {{ old('status', $product->status) == 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                    </div>

                    {{-- VISIBILITY --}}
                    <div class="mb-3">
                        <label>Visibility</label>
                        <select name="visibility" class="form-select">
                            <option value="shop"
                                {{ old('visibility', $product->visibility) == 'shop' ? 'selected' : '' }}>Shop</option>
                            <option value="search"
                                {{ old('visibility', $product->visibility) == 'search' ? 'selected' : '' }}>Search</option>
                            <option value="both"
                                {{ old('visibility', $product->visibility) == 'both' ? 'selected' : '' }}>Shop & Search
                            </option>
                            <option value="hidden"
                                {{ old('visibility', $product->visibility) == 'hidden' ? 'selected' : '' }}>Hidden</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Product</button>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>

                </form>
            </div>
        </div>
    </main>
    <script>
document.addEventListener('DOMContentLoaded', function () {

    /* =========================
       GALLERY IMAGE REMOVE
    ========================== */
    const removedGalleryInput = document.getElementById('removedGalleryImages');

    document.addEventListener('click', function (e) {
        if (!e.target.classList.contains('remove-gallery-image')) return;

        if (!confirm('Remove this image?')) return;

        const item = e.target.closest('.gallery-item');
        const imageId = item.dataset.id;

        item.style.display = 'none';
        item.dataset.removed = '1';

        let removed = removedGalleryInput.value
            ? removedGalleryInput.value.split(',')
            : [];

        if (!removed.includes(imageId)) removed.push(imageId);
        removedGalleryInput.value = removed.join(',');
    });

    document.querySelector('form').addEventListener('submit', function () {
        let removed = [];
        document.querySelectorAll('.gallery-item[data-removed="1"]').forEach(el => {
            removed.push(el.dataset.id);
        });
        removedGalleryInput.value = removed.join(',');
    });

    /* =========================
       PRODUCT TYPE TOGGLE
    ========================== */
    const typeSelect  = document.getElementById('product_type');
    const simpleBox   = document.getElementById('simple-product-fields');
    const variableBox = document.getElementById('variable-attributes');

    function toggleType() {
        const isVariable = typeSelect.value === 'variable';

        simpleBox.style.display   = isVariable ? 'none' : 'block';
        variableBox.style.display = isVariable ? 'block' : 'none';

        simpleBox.querySelectorAll('input, select, textarea')
            .forEach(el => el.disabled = isVariable);

        variableBox.querySelectorAll('input, select, textarea, button')
            .forEach(el => el.disabled = !isVariable);
    }

    typeSelect.addEventListener('change', toggleType);
    toggleType();

    /* =========================
       STOCK TOGGLE
    ========================== */
    const manageStock = document.getElementById('manage_stock');
    if (manageStock) {
        manageStock.addEventListener('change', function () {
            document.getElementById('stock-fields').style.display =
                this.checked ? 'block' : 'none';
        });
        manageStock.dispatchEvent(new Event('change'));
    }

    /* =========================
       GENERATE VARIATIONS - FIXED
    ========================== */
    document.getElementById('generate-variations').addEventListener('click', () => {

        let attributes = {};

        document.querySelectorAll('.attribute-select').forEach(select => {
            const attrId = select.dataset.attributeId;

            const values = [...select.selectedOptions].map(opt => ({
                id: opt.value,
                name: opt.text
            }));

            if (values.length) attributes[attrId] = values;
        });

        if (!Object.keys(attributes).length) {
            alert('Select attributes first');
            return;
        }

        // Generate all possible combinations
        const combos = Object.values(attributes)
            .reduce((a, b) => a.flatMap(d => b.map(e => [...d, e])), [[]]);

        const tbody = document.querySelector('#variation-table tbody');

        // Get existing combination keys from table rows
        const existingCombos = new Set();
        tbody.querySelectorAll('tr[data-combo]').forEach(row => {
            existingCombos.add(row.dataset.combo);
        });

        console.log('Existing combos:', Array.from(existingCombos));

        // Add only new combinations
        combos.forEach(combo => {
            // Create a sorted key for this combination
            const comboKey = combo.map(v => v.id).sort((a, b) => a - b).join('-');

            console.log('Checking combo:', comboKey);

            // Skip if this combination already exists
            if (existingCombos.has(comboKey)) {
                console.log('Skipping existing combo:', comboKey);
                return;
            }

            const index = tbody.children.length;
            const label = combo.map(v => v.name).join(' / ');

            console.log('Adding new combo:', comboKey);

            // Create hidden inputs for attributes (ensure no duplicates)
            const uniqueCombo = [...new Set(combo.map(v => v.id))];
            const attributeInputs = uniqueCombo.map(id =>
                `<input type="hidden" name="variations[${index}][attributes][]" value="${id}">`
            ).join('');

            tbody.insertAdjacentHTML('beforeend', `
                <tr data-combo="${comboKey}">
                    <td class="variation-label">${label}</td>
                    ${attributeInputs}
                    <td><input type="text" name="variations[${index}][sku]" class="form-control"></td>
                    <td><input type="number" step="0.01" name="variations[${index}][regular_price]" class="form-control" required></td>
                    <td><input type="number" name="variations[${index}][stock_quantity]" class="form-control" value="0"></td>
                    <td>
                        <input type="file" name="variations[${index}][image]" class="variation-image-input">
                        <img class="variation-preview mt-1" style="max-width:60px; display:none;">
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-variation">Remove</button>
                    </td>
                </tr>
            `);

            // Add to existing set to prevent duplicates within this generation
            existingCombos.add(comboKey);
        });
    });

    /* =========================
       VARIATION IMAGE PREVIEW
    ========================== */
    document.addEventListener('change', function (e) {
        if (!e.target.classList.contains('variation-image-input')) return;

        const preview = e.target.nextElementSibling;
        const file = e.target.files[0];

        if (!file) return;

        const reader = new FileReader();
        reader.onload = () => {
            preview.src = reader.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    });

    /* =========================
       REMOVE VARIATION & IMAGES
    ========================== */
    let removedVariationImages = [];
    let removedVariations = [];

    document.addEventListener('click', function(e) {
        // Remove variation image
        if (e.target.classList.contains('remove-variation-image')) {
            if (!confirm('Remove variation image?')) return;

            const variationId = e.target.dataset.id;
            if (variationId) removedVariationImages.push(variationId);
            document.getElementById('removedVariationImages').value = removedVariationImages.join(',');
            e.target.closest('div').remove();
        }

        // Remove entire variation
        if (e.target.classList.contains('remove-variation')) {
            if (!confirm('Remove this variation?')) return;

            const tr = e.target.closest('tr');
            const variationId = tr.dataset.id;

            // Only track removal if this is an existing variation (has ID)
            if (variationId) {
                removedVariations.push(variationId);
                document.getElementById('removedVariations').value = removedVariations.join(',');
            }

            tr.remove();
        }
    });

});
    </script>


@endsection
