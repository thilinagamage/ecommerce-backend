<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\GiftCard;
use App\Models\User;
use Illuminate\Http\Request;

class GiftCardController extends Controller
{
    public function index(Request $request)
    {
        $query = GiftCard::with(['purchaser', 'recipient']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('recipient_email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $giftCards = $query->latest()->paginate(20);

        // Statistics
        $stats = [
            'total' => GiftCard::count(),
            'active' => GiftCard::where('status', 'active')->where('balance', '>', 0)->count(),
            'total_balance' => GiftCard::where('status', 'active')->sum('balance'),
            'redeemed' => GiftCard::where('status', 'redeemed')->count(),
        ];

        return view('marketing.gift-cards.index', compact('giftCards', 'stats'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('marketing.gift-cards.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'initial_balance' => 'required|numeric|min:1',
            'purchaser_id' => 'nullable|exists:users,id',
            'recipient_id' => 'nullable|exists:users,id',
            'recipient_email' => 'required|email',
            'message' => 'nullable|string',
            'expires_at' => 'nullable|date|after:today',
            'status' => 'required|in:active,inactive,cancelled',
        ]);

        $validated['code'] = GiftCard::generateCode();
        $validated['balance'] = $validated['initial_balance'];

        $giftCard = GiftCard::create($validated);

        // Create initial transaction
        $giftCard->transactions()->create([
            'type' => 'purchase',
            'amount' => $validated['initial_balance'],
            'balance_after' => $validated['initial_balance'],
            'note' => 'Gift card purchased',
        ]);

        return redirect()->route('marketing.gift-cards.index')
            ->with('success', 'Gift card created successfully. Code: ' . $giftCard->code);
    }

    public function show($id)
    {
        $giftCard = GiftCard::with(['purchaser', 'recipient', 'transactions.order'])->findOrFail($id);
        return view('marketing.gift-cards.show', compact('giftCard'));
    }

    public function update(Request $request, $id)
    {
        $giftCard = GiftCard::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:active,redeemed,expired,cancelled',
            'balance' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $oldBalance = $giftCard->balance;
        $giftCard->update([
            'status' => $validated['status'],
            'balance' => $validated['balance'],
        ]);

        // Log adjustment if balance changed
        if ($oldBalance != $validated['balance']) {
            $giftCard->transactions()->create([
                'type' => 'adjusted',
                'amount' => $validated['balance'] - $oldBalance,
                'balance_after' => $validated['balance'],
                'note' => $validated['note'] ?? 'Manual adjustment by admin',
            ]);
        }

        return back()->with('success', 'Gift card updated successfully');
    }

    // Validate gift card
    public function validate(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $giftCard = GiftCard::where('code', $request->code)->first();

        if (!$giftCard) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid gift card code'
            ]);
        }

        if ($giftCard->status !== 'active') {
            return response()->json([
                'valid' => false,
                'message' => 'Gift card is not active'
            ]);
        }

        if ($giftCard->balance <= 0) {
            return response()->json([
                'valid' => false,
                'message' => 'Gift card has no remaining balance'
            ]);
        }

        if ($giftCard->expires_at && $giftCard->expires_at->isPast()) {
            return response()->json([
                'valid' => false,
                'message' => 'Gift card has expired'
            ]);
        }

        $availableAmount = $request->amount
            ? min($giftCard->balance, $request->amount)
            : $giftCard->balance;

        return response()->json([
            'valid' => true,
            'balance' => $giftCard->balance,
            'available_amount' => $availableAmount,
            'code' => $giftCard->code
        ]);
    }

    // Check balance
    public function checkBalance(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $giftCard = GiftCard::where('code', $request->code)->first();

        if (!$giftCard) {
            return response()->json(['error' => 'Gift card not found'], 404);
        }

        return response()->json([
            'code' => $giftCard->code,
            'balance' => $giftCard->balance,
            'initial_balance' => $giftCard->initial_balance,
            'status' => $giftCard->status,
            'expires_at' => $giftCard->expires_at?->format('Y-m-d'),
        ]);
    }
}
