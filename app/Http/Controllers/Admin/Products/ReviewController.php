<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Product\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
   public function index(Request $request)
    {
        $query = ProductReview::with(['product', 'user'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reviewer_name', 'like', "%{$search}%")
                  ->orWhere('reviewer_email', 'like', "%{$search}%")
                  ->orWhere('comment', 'like', "%{$search}%");
            });
        }

        $reviews = $query->paginate(20);

        // Get filter options
        $products = Product::orderBy('name')->get();

        // Get review statistics
        $stats = [
            'total' => ProductReview::count(),
            'pending' => ProductReview::where('status', 'pending')->count(),
            'approved' => ProductReview::where('status', 'approved')->count(),
            'rejected' => ProductReview::where('status', 'rejected')->count(),
            'average_rating' => round(ProductReview::approved()->avg('rating'), 1),
        ];

        return view('products.reviews.index', compact('reviews', 'products', 'stats'));
    }

public function show($id)
{
    // Remove 'helpfulVotes' from the eager loading
    $review = ProductReview::with(['product', 'user'])->findOrFail($id);
    return view('products.reviews.show', compact('review'));
}

    public function create()
    {
        $products = Product::where('status', 'published')->orderBy('name')->get();
        return view('products.reviews.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'reviewer_name' => 'required|string|max:255',
            'reviewer_email' => 'required|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string',
            'status' => 'required|in:pending,approved,rejected,spam',
            'verified_purchase' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated['verified_purchase'] = $request->has('verified_purchase');

        // Handle image uploads
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('reviews', 'public');
                $imagePaths[] = $path;
            }
            $validated['images'] = $imagePaths;
        }

        ProductReview::create($validated);

        return redirect()->route('products.reviews.index')
            ->with('success', 'Review created successfully');
    }

    public function edit($id)
    {
        $review = ProductReview::findOrFail($id);
        $products = Product::orderBy('name')->get();
        return view('products.reviews.edit', compact('review', 'products'));
    }

    public function update(Request $request, $id)
    {
        $review = ProductReview::findOrFail($id);

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'reviewer_name' => 'required|string|max:255',
            'reviewer_email' => 'required|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string',
            'status' => 'required|in:pending,approved,rejected,spam',
            'verified_purchase' => 'boolean',
            'admin_reply' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'removed_images' => 'nullable|string',
        ]);

        $validated['verified_purchase'] = $request->has('verified_purchase');

        // Handle admin reply timestamp
        if ($request->filled('admin_reply') && empty($review->admin_reply)) {
            $validated['admin_reply_at'] = now();
        }

        // Handle removed images
        if ($request->filled('removed_images')) {
            $removedImages = explode(',', $request->removed_images);
            $currentImages = $review->images ?? [];

            foreach ($removedImages as $imagePath) {
                Storage::disk('public')->delete($imagePath);
                $currentImages = array_filter($currentImages, fn($img) => $img !== $imagePath);
            }

            $validated['images'] = array_values($currentImages);
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $existingImages = $validated['images'] ?? $review->images ?? [];

            foreach ($request->file('images') as $image) {
                $path = $image->store('reviews', 'public');
                $existingImages[] = $path;
            }

            $validated['images'] = $existingImages;
        }

        $review->update($validated);

        return redirect()->route('products.reviews.index')
            ->with('success', 'Review updated successfully');
    }

    public function destroy($id)
    {
        $review = ProductReview::findOrFail($id);

        // Delete associated images
        if ($review->images) {
            foreach ($review->images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $review->delete();

        return redirect()->route('products.reviews.index')
            ->with('success', 'Review deleted successfully');
    }

    // Bulk actions
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,spam,delete',
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:product_reviews,id',
        ]);

        $reviews = ProductReview::whereIn('id', $request->review_ids);

        switch ($request->action) {
            case 'approve':
                $reviews->update(['status' => 'approved']);
                $message = 'Reviews approved successfully';
                break;
            case 'reject':
                $reviews->update(['status' => 'rejected']);
                $message = 'Reviews rejected successfully';
                break;
            case 'spam':
                $reviews->update(['status' => 'spam']);
                $message = 'Reviews marked as spam';
                break;
            case 'delete':
                $reviews->delete();
                $message = 'Reviews deleted successfully';
                break;
        }

        return redirect()->route('products.reviews.index')
            ->with('success', $message);
    }

    // Quick status change
    public function updateStatus(Request $request, $id)
    {
        $review = ProductReview::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,approved,rejected,spam',
        ]);

        $review->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }

    // Add admin reply
    public function addReply(Request $request, $id)
    {
        $review = ProductReview::findOrFail($id);

        $request->validate([
            'admin_reply' => 'required|string',
        ]);

        $review->addAdminReply($request->admin_reply);

        return back()->with('success', 'Reply added successfully');
    }
}
