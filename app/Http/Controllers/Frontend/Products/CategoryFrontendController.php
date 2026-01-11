<?php

namespace App\Http\Controllers\Frontend\Products;

use App\Http\Controllers\Controller;
use App\Models\Product\Category;
use App\Models\Product\CategorySlug;
use Illuminate\Http\Request;

class CategoryFrontendController extends Controller
{
    public function index($slug)
{
    $category = Category::where('slug', $slug)->first();

    if (!$category) {
        $old = CategorySlug::where('old_slug', $slug)->first();

        if ($old) {
            return redirect()
                ->route('category.show', $old->category->slug)
                ->setStatusCode(301);
        }

        abort(404);
    }

    return view('categories.show', compact('category'));
}

}
