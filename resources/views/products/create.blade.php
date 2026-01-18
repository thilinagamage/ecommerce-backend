@extends('layouts.admin.layout')

@section('content')
<main class="page-content">
<div class="card">
<div class="card-body">

<h4 class="card-title mb-4">Create Product</h4>

{{-- Errors --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
@csrf

{{-- BASIC INFO --}}
<div class="mb-3">
    <label class="form-label">Product Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Slug</label>
    <input type="text" name="slug" class="form-control" value="{{ old('slug') }}">
</div>

<div class="mb-3">
    <label class="form-label">Short Description</label>
    <textarea name="short_description" class="form-control">{{ old('short_description') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
</div>

{{-- IMAGES --}}
<div class="mb-3">
    <label class="form-label">Featured Image</label>
    <input type="file" name="featured_image" class="form-control">
</div>

<div class="mb-3">
    <label class="form-label">Gallery Images</label>
    <input type="file" name="gallery_images[]" multiple class="form-control">
</div>

{{-- CATEGORIES --}}
<div class="mb-3">
    <label class="form-label">Categories</label>
    @foreach($categories as $category)
        <div>
            <label>
                <input type="checkbox" name="categories[]" value="{{ $category->id }}">
                {{ $category->name }}
            </label>
        </div>
    @endforeach
</div>

{{-- TAGS --}}
<div class="mb-3">
    <label class="form-label">Tags</label>
    <select name="tags[]" class="form-control" multiple>
        @foreach($tags as $tag)
            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
        @endforeach
    </select>
</div>

{{-- PRODUCT TYPE --}}
<div class="mb-3">
    <label class="form-label">Product Type</label>
    <select name="product_type" id="product_type" class="form-select">
        <option value="simple" {{ old('product_type') === 'simple' ? 'selected' : '' }}>Simple</option>
        <option value="variable" {{ old('product_type') === 'variable' ? 'selected' : '' }}>Variable</option>
    </select>
</div>

{{-- SIMPLE PRODUCT --}}
<div id="simple-product-fields">

<h5 class="mt-4">Inventory</h5>

<div class="mb-3">
    <label>SKU</label>
    <input type="text" name="sku" class="form-control">
</div>

<div class="form-check mb-3">
    <input type="checkbox" name="manage_stock" value="1" class="form-check-input" id="manage_stock">
    <label class="form-check-label">Manage stock?</label>
</div>

<div id="stock-fields" style="display:none">
    <div class="mb-3">
        <label>Stock Quantity</label>
        <input type="number" name="stock_quantity" class="form-control">
    </div>

    <div class="mb-3">
        <label>Low Stock Threshold</label>
        <input type="number" name="low_stock_threshold" class="form-control">
    </div>

    <div class="form-check mb-3">
        <input type="checkbox" name="backorders" value="1" class="form-check-input">
        <label class="form-check-label">Allow backorders?</label>
    </div>
</div>

<h5 class="mt-4">Pricing</h5>

<div class="mb-3">
    <label>Regular Price</label>
    <input type="number" step="0.01" name="regular_price" class="form-control">
</div>

<div class="mb-3">
    <label>Sale Price</label>
    <input type="number" step="0.01" name="sale_price" class="form-control">
</div>

</div>

{{-- VARIABLE PRODUCT --}}
<div id="variable-attributes" style="display:none">

<h5 class="mt-4">Attributes</h5>

@foreach($attributes as $attribute)
    <div class="mb-3">
        <label>{{ $attribute->name }}</label>
        <select class="form-control attribute-select"
                data-attribute-id="{{ $attribute->id }}"
                multiple>
            @foreach($attribute->values as $value)
                <option value="{{ $value->id }}">{{ $value->value }}</option>
            @endforeach
        </select>
    </div>
@endforeach

<button type="button" id="generate-variations" class="btn btn-primary mb-3">
    Generate Variations
</button>

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
    <tbody></tbody>
</table>

</div>

{{-- COLLECTION --}}
<div class="mb-3">
    <label class="form-label">Collection</label>
    <select name="collection_id" class="form-select">
        <option value="">-- Select --</option>
        @foreach($collections as $collection)
            <option value="{{ $collection->id }}">{{ $collection->name }}</option>
        @endforeach
    </select>
</div>

{{-- STATUS --}}
<div class="mb-3">
    <label>Status</label>
    <select name="status" class="form-select">
        <option value="draft">Draft</option>
        <option value="published">Published</option>
    </select>
</div>

{{-- VISIBILITY --}}
<div class="mb-3">
    <label>Visibility</label>
    <select name="visibility" class="form-select">
        <option value="shop">Shop</option>
        <option value="search">Search</option>
        <option value="both">Both</option>
        <option value="hidden">Hidden</option>
    </select>
</div>

<button type="submit" class="btn btn-primary">Create Product</button>
<a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>

</form>
</div>
</div>
</main>

{{-- SCRIPTS --}}
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Stock toggle
    document.getElementById('manage_stock').addEventListener('change', function() {
        document.getElementById('stock-fields').style.display = this.checked ? 'block' : 'none';
    });

    // Product type toggle
    const typeSelect = document.getElementById('product_type');
    const simpleBox = document.getElementById('simple-product-fields');
    const variableBox = document.getElementById('variable-attributes');

    function toggleType() {
        if (typeSelect.value === 'variable') {
            simpleBox.style.display = 'none';
            variableBox.style.display = 'block';

            // Enable all inputs in variable box
            variableBox.querySelectorAll('input, select, textarea, button').forEach(el => el.disabled = false);

            // Disable simple product inputs
            simpleBox.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
        } else {
            simpleBox.style.display = 'block';
            variableBox.style.display = 'none';

            // Enable simple product inputs
            simpleBox.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);

            // Disable all inputs in variable box
            variableBox.querySelectorAll('input, select, textarea, button').forEach(el => el.disabled = true);
        }
    }

    typeSelect.addEventListener('change', toggleType);
    toggleType(); // Run on page load

    // Generate variations
    document.getElementById('generate-variations').addEventListener('click', () => {
        let attributes = {};

        // Collect selected attribute values (store ID and name)
        document.querySelectorAll('.attribute-select').forEach(select => {
            const attrId = select.dataset.attributeId;
            const values = [...select.selectedOptions].map(opt => ({
                id: opt.value,
                name: opt.textContent
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

        // Existing combos (to avoid duplicates)
        const existingCombos = Array.from(tbody.querySelectorAll('tr')).map(row =>
            [...row.querySelectorAll('input[name*="[attributes][]"]')].map(i => i.value).join('-')
        );

        combos.forEach(combo => {
            const comboKey = combo.map(v => v.id).join('-');
            if (existingCombos.includes(comboKey)) return; // skip existing

            const index = tbody.children.length;

            tbody.innerHTML += `
            <tr>
                <td>${combo.map(v => v.name).join(' / ')}</td>
                <td><input type="text" name="variations[${index}][sku]" class="form-control"></td>
                <td><input type="number" step="0.01" name="variations[${index}][regular_price]" class="form-control"></td>
                <td><input type="number" name="variations[${index}][stock_quantity]" class="form-control"></td>
                <td>
                    <input type="file" name="variations[${index}][image]" class="variation-image-input">
                    <img class="variation-preview mt-1" style="max-width:60px; display:none;">
                </td>
                <td><button type="button" class="btn btn-sm btn-danger remove-variation"> Remove</button></td>
                ${combo.map(v => `<input type="hidden" name="variations[${index}][attributes][]" value="${v.id}">`).join('')}
            </tr>`;
        });
    });

    // Remove variation
    let removedVariations = [];
    document.addEventListener('click', function(e) {
        if (!e.target.classList.contains('remove-variation')) return;
        if (!confirm('Remove this variation?')) return;

        const row = e.target.closest('tr');
        const variationId = row.dataset.id;

        if (variationId) {
            removedVariations.push(variationId);
            document.getElementById('removedVariations').value = removedVariations.join(',');
        }

        row.remove();
    });

    // Variation image preview
    document.addEventListener('change', function(e) {
        if (!e.target.classList.contains('variation-image-input')) return;

        const preview = e.target.nextElementSibling;
        const file = e.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = () => {
                preview.src = reader.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

});
</script>

@endsection
