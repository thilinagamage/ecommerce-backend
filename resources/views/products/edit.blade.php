@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

                <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card">
                        <div class="card-body">

                            {{-- BASIC INFO --}}
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label>Product Name *</label>
                                        <input name="name" class="form-control" required
                                            value="{{ old('name', $product->name) }}">
                                    </div>

                                    <div class="mb-3">
                                        <label>Slug</label>
                                        <input name="slug" class="form-control"
                                            value="{{ old('slug', $product->slug) }}">
                                    </div>

                                    <div class="mb-3">
                                        <label>Short Description</label>
                                        <textarea name="short_description" class="form-control">{{ old('short_description', $product->short_description) }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control">{{ old('description', $product->description) }}</textarea>
                                    </div>

                                    {{-- Featured Image --}}

                                    <div class="mb-3">
                                        <label class="form-label">Product Image</label>
                                        <input type="file" name="featured_image" class="form-control">
                                        @if ($product->featuredImage)
                                            <img src="{{ $product->featuredImage->url }}" class="mt-2" width="120">
                                        @endif
                                    </div>

                                    {{-- Product Gallery --}}
                                    {{-- <div class="mb-4">
                      <h5 class="mb-3">Display images</h5>
                      <input id="fancy-file-upload" type="file" name="gallery[]" accept=".jpg, .png, image/jpeg, image/png" multiple>
                        <div class="row mb-2">
                            @foreach ($product->images as $image)
                                <div class="col-3 text-center existing-gallery">
                                    <img src="{{ asset('storage/' . $image->path) }}" class="img-fluid mb-1" style="height: 100px; object-fit: cover;">
                                    <button type="button" class="btn btn-sm btn-danger remove-image" data-id="{{ $image->id }}">Remove</button>
                                </div>
                            @endforeach
                        </div>
                    </div> --}}
                                    <div class="mb-3">
                                        <label class="form-label">Product Gallery</label>
                                        {{-- <input type="file" name="gallery[]" class="form-control" multiple> --}}
                                        {{-- <input id="fancy-file-upload" type="file" name="gallery[]" accept=".jpg, .png, image/jpeg, image/png" multiple> --}}
                                        <input type="file" id="gallery_images" name="gallery_images[]"
                                            class="form-control" multiple accept="image/*">
                                        <div id="galleryPreview" class="row mt-3">
                                            <div class="row" id="existing-gallery">
                                                @foreach ($product->images as $image)
                                                    <div class="col-md-2 mb-3 gallery-item" data-id="{{ $image->id }}">
                                                        <div class="position-relative">
                                                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                                                class="img-fluid rounded border">

                                                            <button type="button"
                                                                class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-existing">
                                                                ×
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            {{-- Store removed IDs --}}
                                            <div id="removed-images-wrapper"></div>


                                        </div>


                                    </div>


                                    {{-- Status & Type --}}
                                    <div class="row">
                                        <div class="col">
                                            <label>Status</label>
                                                <select name="status" class="form-select">
                                                    <option value="0" {{ !$product->status ? 'selected' : '' }}>Draft</option>
                                                    <option value="1" {{ $product->status ? 'selected' : '' }}>Published</option>
                                                </select>

                                        </div>

                                        <div class="col">
                                            <label>Product Type</label>
                                            <select name="type" id="productType" class="form-select">
                                                <option value="simple" {{ $product->isSimple() ? 'selected' : '' }}>Simple
                                                    Product</option>
                                                <option value="variable" {{ $product->isVariable() ? 'selected' : '' }}>
                                                    Variable Product</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            {{-- SIMPLE PRODUCT SECTION --}}
                            <div id="simpleSection" style="{{ $product->isVariable() ? 'display:none' : 'block' }}">
                                <div class="row">
                                    <div class="col">
                                        <label>Price</label>
                                        <input name="price" class="form-control"
                                            value="{{ old('price', $product->price) }}">
                                    </div>
                                    <div class="col">
                                        <label>Sale Price</label>
                                        <input name="sale_price" class="form-control"
                                            value="{{ old('sale_price', $product->sale_price) }}">
                                    </div>
                                    <div class="col">
                                        <label>Quantity</label>
                                        <input name="qty" class="form-control"
                                            value="{{ old('qty', $product->variations->first()->stock ?? 0) }}">
                                    </div>
                                </div>
                            </div>

                            {{-- VARIABLE PRODUCT SECTION --}}
                            <div id="variableSection" style="{{ $product->isVariable() ? 'block' : 'display:none' }}">

                                {{-- ATTRIBUTES --}}
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6>Attributes</h6>
                                        @foreach ($attributes as $attribute)
                                            <div class="mb-3">
                                                <label>{{ $attribute->name }}</label>
                                                <select class="form-select multiple-select attribute-select"
                                                    data-attribute-id="{{ $attribute->id }}" multiple="multiple">
                                                    @foreach ($attribute->values as $value)
                                                        <option value="{{ $value->id }}"
                                                            @foreach ($product->variations as $variation)
                                                        @foreach ($variation->attributes as $vattr)
                                                            @if ($vattr->product_attribute_id == $attribute->id && $vattr->product_attribute_value_id == $value->id)
                                                                selected
                                                            @endif @endforeach
                                                            @endforeach
                                                            >
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

                                {{-- EXISTING VARIATIONS --}}
                                <div id="variationsWrapper">
                                    @foreach ($product->variations as $i => $variation)
                                        <div class="card mb-2 variation-card">
                                            <div class="card-body">
                                                <strong>
                                                    {{ $variation->attributes->map(fn($a) => $a->value->value)->join(' / ') }}
                                                </strong>
                                                <div class="row g-2 mt-2">
                                                    <div class="col">
                                                        <input name="variations[{{ $i }}][sku]"
                                                            class="form-control" placeholder="SKU"
                                                            value="{{ $variation->sku }}">
                                                    </div>
                                                    <div class="col">
                                                        <input name="variations[{{ $i }}][price]"
                                                            class="form-control variation-price" placeholder="Price"
                                                            value="{{ $variation->price }}">
                                                    </div>
                                                    <div class="col">
                                                        <input name="variations[{{ $i }}][sale_price]"
                                                            class="form-control variation-sale-price" placeholder="Sale"
                                                            value="{{ $variation->sale_price }}">
                                                    </div>
                                                    <div class="col">
                                                        <input name="variations[{{ $i }}][qty]"
                                                            class="form-control variation-qty" placeholder="Qty"
                                                            value="{{ $variation->stock }}">
                                                    </div>
                                                </div>

                                                <div class="mt-2">
                                                    <input type="file" name="variations[{{ $i }}][image]"
                                                        class="form-control">
                                                    @if ($variation->defaultImage)
                                                        <img src="{{ $variation->defaultImage->url }}" width="80">
                                                    @endif
                                                </div>

                                                <button type="button"
                                                    class="btn btn-sm btn-danger remove-variation">✕</button>

                                                @foreach ($variation->attributes as $vattr)
                                                    <input type="hidden"
                                                        name="variations[{{ $i }}][attributes][{{ $vattr->product_attribute_id }}]"
                                                        value="{{ $vattr->product_attribute_value_id }}">
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>

                            {{-- SEO --}}
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6>SEO</h6>

                                    <div class="mb-3">
                                        <label>Meta Title</label>
                                        <input name="meta_title" class="form-control"
                                            value="{{ old('meta_title', $product->meta_title) }}">
                                    </div>

                                    <div class="mb-3">
                                        <label>Meta Description</label>
                                        <textarea name="meta_description" class="form-control">{{ old('meta_description', $product->meta_description) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success mt-3">Update Product</button>

                        </div>
                    </div>
                </form>

            </div>
        </div>
    </main>

    {{-- JS: same as create but will handle remove existing variations --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const productType = document.getElementById('productType');
            const variableSection = document.getElementById('variableSection');
            const generateBtn = document.getElementById('generateVariations');
            const variationsWrapper = document.getElementById('variationsWrapper');

            // Toggle simple / variable
            productType.addEventListener('change', e => {
                const isVariable = e.target.value === 'variable';
                variableSection.style.display = isVariable ? 'block' : 'none';
                document.getElementById('simpleSection').style.display = isVariable ? 'none' : 'block';
            });

            // Generate new variations
            generateBtn.addEventListener('click', () => {
                const attrs = [];
                document.querySelectorAll('.attribute-select').forEach(select => {
                    const values = [...select.selectedOptions].map(o => ({
                        id: o.value,
                        label: o.text
                    }));
                    if (values.length) attrs.push({
                        attrId: select.dataset.attributeId,
                        values
                    });
                });
                if (!attrs.length) {
                    alert('Select at least one attribute value');
                    return;
                }

                const cartesian = arr => arr.reduce((a, b) => a.flatMap(d => b.values.map(e => [...d, {
                    attrId: b.attrId,
                    valueId: e.id,
                    label: e.label
                }])), [
                    []
                ]);
                const combos = cartesian(attrs);
                variationsWrapper.innerHTML = '';
                combos.forEach((combo, i) => {
                    let html = `<div class="card mb-2 variation-card"><div class="card-body">
                <strong>${combo.map(c=>c.label).join(' / ')}</strong>
                <div class="row g-2 mt-2">
                    <div class="col"><input name="variations[${i}][sku]" class="form-control" placeholder="SKU"></div>
                    <div class="col"><input name="variations[${i}][price]" class="form-control variation-price" placeholder="Price"></div>
                    <div class="col"><input name="variations[${i}][sale_price]" class="form-control variation-sale-price" placeholder="Sale"></div>
                    <div class="col"><input name="variations[${i}][qty]" class="form-control variation-qty" placeholder="Qty"></div>
                </div>
                <div class="mt-2"><input type="file" name="variations[${i}][image]" class="form-control"></div>
                <button type="button" class="btn btn-sm btn-danger remove-variation">✕</button>`;
                    combo.forEach(c => {
                        html +=
                            `<input type="hidden" name="variations[${i}][attributes][${c.attrId}]" value="${c.valueId}">`
                    });
                    html += `</div></div>`;
                    variationsWrapper.insertAdjacentHTML('beforeend', html);
                });
            });

            // Remove variation
            document.addEventListener('click', e => {
                if (e.target.classList.contains('remove-variation')) {
                    e.target.closest('.variation-card').remove();
                }
            });

            // Bulk apply
            document.getElementById('applyBulk').addEventListener('click', () => {
                const price = document.getElementById('bulk_price').value;
                const sale = document.getElementById('bulk_sale_price').value;
                const qty = document.getElementById('bulk_qty').value;
                document.querySelectorAll('.variation-price').forEach(i => i.value = price);
                document.querySelectorAll('.variation-sale-price').forEach(i => i.value = sale);
                document.querySelectorAll('.variation-qty').forEach(i => i.value = qty);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {

    const preview = document.getElementById('galleryPreview');
    const existing = document.getElementById('existing-gallery');

            input.addEventListener('change', function() {


                Array.from(this.files).forEach((file, index) => {
                    if (!file.type.startsWith('image/')) return;

                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'col-md-2 position-relative mb-2';

                        div.innerHTML = `
                    <img src="${e.target.result}" class="img-fluid rounded border">
                    <button type="button"
                        class="btn btn-danger btn-sm position-absolute top-0 end-0 remove-preview">
                        ×
                    </button>
                `;

                        preview.appendChild(div);

                        div.querySelector('.remove-preview').addEventListener('click', () => {
                            div.remove();
                        });
                    };

                    reader.readAsDataURL(file);
                });
            });

        });



        $(document).on('click', '.remove-existing', function() {
            const item = $(this).closest('.gallery-item');
            const imageId = item.data('id');

            // Add hidden input
            $('#removed-images-wrapper').append(`
        <input type="hidden" name="removed_images[]" value="${imageId}">
    `);

            // Hide visually only
            item.fadeOut(300);
        });
    </script>
@endsection
