<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Product\Category;
use App\Models\Product\Product;
use App\Models\Product\ProductAttribute;
use App\Models\Product\ProductImage;
use App\Models\Product\ProductVariation;
use App\Models\Product\ProductVariationAttribute;
use App\Models\Product\ProductVariationImage;
use App\Services\ProductVariationGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller

{
       public function index()
    {
        $products = Product::latest()->paginate(20);
        return view('products.index', compact('products'));
    }

        public function create()
    {

    }


    public function store()
    {


    }


    public function edit($id)
    {

    }

    public function update(Request $request, $id)
    {

    }


    public function removeImage(ProductImage $image)
    {

    }
}
