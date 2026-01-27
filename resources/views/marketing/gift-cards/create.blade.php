@extends('layouts.admin.layout')

@section('content')
<main class="page-content">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-4">Create Gift Card</h4>

            <form method="POST" action="{{ route('marketing.gift-cards.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Initial Balance <span class="text-danger">*</span></label>
                    <input type="number" name="initial_balance" class="form-control" step="0.01" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Recipient Email <span class="text-danger">*</span></label>
                    <input type="email" name="recipient_email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Purchaser (Optional)</label>
                    <select name="purchaser_id" class="form-select">
                        <option value="">-- None --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Message (Optional)</label>
                    <textarea name="message" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Expiry Date (Optional)</label>
                    <input type="date" name="expires_at" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Create Gift Card</button>
                <a href="{{ route('marketing.gift-cards.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</main>
@endsection
