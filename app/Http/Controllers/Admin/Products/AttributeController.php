<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Product\ProductAttribute;
use App\Models\Product\ProductAttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
        public function index()
    {
        $attributes = ProductAttribute::with('values')->orderBy('name')->get();
        return view('products.attributes.index', compact('attributes'));
    }

    /**
     * Show the form to create a new attribute
     */
    public function create()
    {
        $attributes = ProductAttribute::with('values')->orderBy('name')->get();
        return view('products.attributes.create', compact('attributes'));
    }

    /**
     * Store a new attribute
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255|unique:product_attributes,name',
            'values' => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            $attribute = ProductAttribute::create([
                'name' => $request->name,
                 'slug' => Str::slug($request->name), // add this line
            ]);

            // Save attribute values
            $values = explode(',', $request->values);
            foreach ($values as $value) {
                ProductAttributeValue::create([
                'product_attribute_id' => $attribute->id,
                'value'                => trim($value),
                'slug'                 => Str::slug(trim($value)), // <-- add this
            ]);
            }
        });

        return redirect()->route('products.attributes.create')
                         ->with('success', 'Attribute created successfully.');
    }

    /**
     * Show the form to edit an attribute
     */
    public function edit(ProductAttribute $attribute)
    {
        $attribute->load('values');
        return view('products.attributes.edit', compact('attribute'));
    }

    /**
     * Update an attribute
     */
    public function update(Request $request, ProductAttribute $attribute)
    {
        $request->validate([
            'name'   => 'required|string|max:255|unique:product_attributes,name,' . $attribute->id,
            'values' => 'required|string',
        ]);

        DB::transaction(function () use ($request, $attribute) {
            $attribute->update([
                'name' => $request->name,
            ]);

            // Delete old values and insert new ones
            $attribute->values()->delete();

            $values = explode(',', $request->values);
            foreach ($values as $value) {
                ProductAttributeValue::create([
                    'product_attribute_id' => $attribute->id,
                    'value' => trim($value),
                    'slug'  => Str::slug(trim($value)),  // <-- add this
                ]);
            }
        });

        return redirect()->route('products.attributes.create')
                         ->with('success', 'Attribute updated successfully.');
    }

    /**
     * Delete an attribute
     */
    public function destroy(ProductAttribute $attribute)
    {
        DB::transaction(function () use ($attribute) {
            $attribute->values()->delete();
            $attribute->delete();
        });

        return redirect()->route('products.attributes.create')
                         ->with('success', 'Attribute deleted successfully.');
    }
}
