<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Product\Category;
use App\Models\Product\CategorySlug;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::with('parent')->orderBy('position');

        // Filter by parent
        if ($request->filled('parent_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('id', $request->parent_id)
                ->orWhere('parent_id', $request->parent_id);
            });
        }

        // Search by name or slug
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('slug', 'like', '%'.$request->search.'%');
            });
        }

        $categories = $query->paginate(20)->withQueryString();
        $parents = Category::whereNull('parent_id')->where('status', 1)->get();

        return view('products.categories.index', compact('categories', 'parents'));
    }


    public function create(){
        $parents = Category::whereNull('parent_id')->where('status',1)->get();
        return view('products.categories.create', compact('parents'));
    }

    public function store(CategoryRequest $request){

            $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'required|boolean',
            'position' => 'nullable|integer',
        ]);

        $categoryImage = null;
        if ($request->hasFile('image')) {
        $categoryImage = $request->file('image')->store('categories', 'public');
    }


        $slug = $this->generateUniqueSlug($request->slug ?? $request->name);

        $category = Category::create([
            'name'              => $request->name,
            'slug'              => $request->slug,
            'description'       => $request->description,
            'parent_id'         => $request->parent_id,
            'category_image'    => $categoryImage,
            'status'           => $request->status ?? 1,
            'position'         => $request->position ?? 0,

            // SEO
            'meta_title'       => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords'    => $request->meta_keywords,
            'canonical_url'    => $request->canonical_url,
            'seo_index'        => $request->seo_index ?? 1,
            'seo_follow'       => $request->seo_follow ?? 1,

        ]);
         return redirect()
            ->route('products.categories.index')
            ->with('success', 'Category created successfully');
    }

    public function edit(Category $category)
    {

        $parents = Category::whereNull('parent_id')
                        ->where('status', 1)
                        ->where('id', '!=', $category->id)
                        ->get();


        return view('products.categories.edit', compact('category', 'parents'));
    }


    public function update(Request $request, Category $category)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id|not_in:' . $category->id,
            'image' => 'nullable|image|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|url|max:255',
            'seo_index' => 'nullable|boolean',
            'seo_follow' => 'nullable|boolean',
        ]);


            $baseSlug = $request->slug
        ? Str::slug($request->slug)
        : Str::slug($request->name);

        $slug = $baseSlug;

        // If slug exists for another category → make it unique
        $count = Category::where('slug', $slug)
            ->where('id', '!=', $category->id)
            ->count();

        if ($count > 0) {
            $slug = $baseSlug . '-' . ($count + 1);
        }


        if ($request->hasFile('image')) {

            if ($category->category_image && file_exists(storage_path('app/public/' . $category->category_image))) {
                unlink(storage_path('app/public/' . $category->category_image));
            }


            $path = $request->file('image')->store('categories', 'public');
            $category->category_image = $path;
        }


        $category->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'parent_id' => $request->parent_id,
            'status' => $request->status ?? 1,
            'position' => $request->position ?? 0,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'canonical_url' => $request->canonical_url,
            'seo_index' => $request->seo_index ?? 1,
            'seo_follow' => $request->seo_follow ?? 1,
        ]);

        return redirect()
            ->route('products.categories.index')
            ->with('success', 'Category updated successfully.');

    }

    public function show(Category $category)
    {

        $children = Category::where('parent_id', $category->id)->get();

        return view('products.categories.show', compact('category', 'children'));
    }

    public function destroy(Category $category){

        Category::where('parent_id', $category->id)
            ->update(['parent_id' => $category->parent_id]);

        $category->delete();

        return redirect()
            ->route('products.categories.index')
            ->with('success', 'Category deleted');
    }
     private function generateUniqueSlug(string $value): string{
        $slug = Str::slug($value);
        $original = $slug;
        $count = 1;

        while (Category::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $count++;
        }

        return $slug;
    }
}
