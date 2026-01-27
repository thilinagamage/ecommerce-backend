<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Product\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $collections = Collection::latest()->paginate(15);
        return view('products.collections.index', compact('collections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.collections.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
          $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:collections,slug',
            'description' => 'nullable|string',
            'banner_image' => 'nullable|image',
            'status' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')
                ->store('collections', 'public');
        }

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        Collection::create($data);

        return redirect()
            ->route('products.collections.index')
            ->with('success', 'Collection created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Collection $collection)
    {
         return view('products.collections.edit', compact('collection'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Collection $collection)
    {
          $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:collections,slug,' . $collection->id,
            'description' => 'nullable|string',
            'banner_image' => 'nullable|image',
            'status' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($request->hasFile('banner_image')) {
            if ($collection->banner_image) {
                Storage::disk('public')->delete($collection->banner_image);
            }

            $data['banner_image'] = $request->file('banner_image')
                ->store('collections', 'public');
        }

        $collection->update($data);

        return redirect()
            ->route('products.collections.index')
            ->with('success', 'Collection updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Collection $collection)
    {
             if ($collection->banner_image) {
            Storage::disk('public')->delete($collection->banner_image);
        }

        $collection->delete();

        return redirect()
            ->route('products.collections.index')
            ->with('success', 'Collection deleted successfully.');
    }

}
