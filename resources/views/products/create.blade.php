@extends('layouts.admin.layout')

@section('content')
<main class="page-content">
    <div class="card">
        <div class="card-body">

                <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="card">
                <div class="card-body">

                    {{-- BASIC INFO --}}
<div class="card mb-3">
    <div class="card-body">
        <div class="mb-3">
            <label>Product Name *</label>
            <input name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Slug</label>
            <input name="slug" class="form-control">
        </div>

        <div class="mb-3">
            <label>Short Description</label>
            <textarea name="short_description" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        {{-- Product Main Image --}}
<div class="mb-3">
    <label class="form-label">Product Image</label>
    <input type="file" name="image" class="form-control">
</div>

<select name="categories[]" multiple>
    @foreach($categories as $category)
        <option value="{{ $category->id }}">{{ $category->name }}</option>
    @endforeach
</select>


{{-- Product Gallery --}}

<div class="mb-3">
    <label class="form-label">Product Gallery</label>
    <input type="file" name="gallery[]" class="form-control" multiple>
</div>

        <div class="row">
            <div class="col">
                <label>Status</label>
                <select name="status" class="form-select">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>

            <div class="col">
                <label>Product Type</label>
                <select name="type" id="productType" class="form-select">
                    <option value="simple">Simple Product</option>
                    <option value="variable">Variable Product</option>
                </select>
            </div>

        </div>
    </div>
</div>


<div id="simpleSection">
    <div class="row">
        <div class="col">
            <label>Price</label>
            <input name="price" class="form-control">
        </div>
        <div class="col">
            <label>Sale Price</label>
            <input name="sale_price" class="form-control">
        </div>
        <div class="col">
            <label>Quantity</label>
            <input name="qty" class="form-control">
        </div>
    </div>
</div>



                    {{-- VARIABLE PRODUCT SECTION --}}
                    <div id="variableSection" style="display:none">

                        {{-- ATTRIBUTES --}}
                        <div class="card mb-3">
                            <div class="card-body">
                                <h6>Attributes</h6>

                                @foreach($attributes as $attribute)
                                    <div class="mb-3">
                                        <label>{{ $attribute->name }}</label>
                                        <select
                                            class="form-select attribute-select"
                                            data-attribute-id="{{ $attribute->id }}"
                                            multiple>
                                            @foreach($attribute->values as $value)
                                                <option value="{{ $value->id }}">
                                                    {{ $value->value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach

                                <button type="button" id="generateVariations" class="btn btn-primary">
                                    Generate Variations
                                </button>
                            </div>
                        </div>

                        {{-- BULK EDIT --}}
<div class="card mb-3">
    <div class="card-body">
        <h6>Bulk Edit</h6>
        <div class="row g-2">
            <div class="col">
                <input id="bulk_price" class="form-control" placeholder="Bulk price">
            </div>
            <div class="col">
                <input id="bulk_sale_price" class="form-control" placeholder="Bulk sale">
            </div>
            <div class="col">
                <input id="bulk_qty" class="form-control" placeholder="Bulk qty">
            </div>
            <div class="col">
                <button type="button" id="applyBulk" class="btn btn-outline-primary">
                    Apply to all
                </button>
            </div>
        </div>
    </div>
</div>


                        {{-- VARIATIONS --}}
                        <div id="variationsWrapper"></div>
                    </div>

                    <div class="card mb-3">

    <div class="card-body">
        <h6>SEO</h6>

        <div class="mb-3">
            <label>Meta Title</label>
            <input name="meta_title" class="form-control">
        </div>

        <div class="mb-3">
            <label>Meta Description</label>
            <textarea name="meta_description" class="form-control"></textarea>
        </div>
    </div>
</div>

                    <button class="btn btn-success mt-3">Save Product</button>


                </div>
                </div>
                </form>


        </div>
    </div>
</main>
<script>
document.addEventListener('DOMContentLoaded', () => {

    const productType = document.getElementById('productType');
    const variableSection = document.getElementById('variableSection');
    const generateBtn = document.getElementById('generateVariations');
    const variationsWrapper = document.getElementById('variationsWrapper');

    // Toggle simple / variable
document.getElementById('productType').addEventListener('change', e => {
    const isVariable = e.target.value === 'variable';

    document.getElementById('variableSection').style.display = isVariable ? 'block' : 'none';
    document.getElementById('simpleSection').style.display = isVariable ? 'none' : 'block';
});

    // Generate variations
    generateBtn.addEventListener('click', () => {

        const attrs = [];

        document.querySelectorAll('.attribute-select').forEach(select => {
            const values = [...select.selectedOptions].map(o => ({
                id: o.value,
                label: o.text
            }));

            if (values.length) {
                attrs.push({
                    attrId: select.dataset.attributeId,
                    values
                });
            }
        });

        if (!attrs.length) {
            alert('Select at least one attribute value');
            return;
        }

        const cartesian = (arr) =>
            arr.reduce((a, b) =>
                a.flatMap(d =>
                    b.values.map(e => [...d, {
                        attrId: b.attrId,
                        valueId: e.id,
                        label: e.label
                    }])
                ), [[]]);

        const combos = cartesian(attrs);
        variationsWrapper.innerHTML = '';

        combos.forEach((combo, i) => {

            let html = `
            <div class="card mb-2 variation-card">
                <div class="card-body">
                    <strong>${combo.map(c => c.label).join(' / ')}</strong>

                    <div class="row g-2 mt-2">
                        <div class="col">
                            <input name="variations[${i}][sku]" class="form-control" placeholder="SKU">
                        </div>
                        <div class="col">
                            <input name="variations[${i}][price]" class="form-control variation-price" placeholder="Price">
                        </div>
                        <div class="col">
                            <input name="variations[${i}][sale_price]" class="form-control variation-sale-price" placeholder="Sale">
                        </div>
                        <div class="col">
                            <input name="variations[${i}][qty]" class="form-control variation-qty" placeholder="Qty">
                        </div>
                    </div>

                    <div class="mt-2">
                        <input type="file" name="variations[${i}][image]" class="form-control">
                    </div>
                                <button type="button" class="btn btn-sm btn-danger remove-variation">✕</button>

            `;

            combo.forEach(c => {
                html += `
                <input type="hidden"
                    name="variations[${i}][attributes][${c.attrId}]"
                    value="${c.valueId}">
                `;
            });

            html += `</div></div>`;
            variationsWrapper.insertAdjacentHTML('beforeend', html);
        });
    });
document.addEventListener('click', e => {
    if (e.target.classList.contains('remove-variation')) {
        e.target.closest('.variation-card').remove();
    }
});

    // Bulk apply
    document.getElementById('applyBulk').addEventListener('click', () => {
        document.querySelectorAll('.variation-price')
            .forEach(i => i.value = document.getElementById('bulk_price').value);

        document.querySelectorAll('.variation-sale-price')
            .forEach(i => i.value = document.getElementById('bulk_sale_price').value);

        document.querySelectorAll('.variation-qty')
            .forEach(i => i.value = document.getElementById('bulk_qty').value);
    });

});
</script>


@endsection
