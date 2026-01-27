@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title mb-4">Edit Order - {{ $order->order_number }}</h4>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('orders.update', $order->id) }}">
                    @csrf
                    @method('PUT')

                    {{-- Customer Information --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Customer Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="customer_name"
                                           class="form-control"
                                           value="{{ old('customer_name', $order->customer_name) }}"
                                           required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email"
                                           name="customer_email"
                                           class="form-control"
                                           value="{{ old('customer_email', $order->customer_email) }}"
                                           required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text"
                                           name="customer_phone"
                                           class="form-control"
                                           value="{{ old('customer_phone', $order->customer_phone) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Billing Address --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Billing Address</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="billing_address_line1"
                                           class="form-control"
                                           value="{{ old('billing_address_line1', $order->billing_address_line1) }}"
                                           required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Address Line 2</label>
                                    <input type="text"
                                           name="billing_address_line2"
                                           class="form-control"
                                           value="{{ old('billing_address_line2', $order->billing_address_line2) }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">City <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="billing_city"
                                           class="form-control"
                                           value="{{ old('billing_city', $order->billing_city) }}"
                                           required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">State/Province</label>
                                    <input type="text"
                                           name="billing_state"
                                           class="form-control"
                                           value="{{ old('billing_state', $order->billing_state) }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Postal Code <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="billing_postal_code"
                                           class="form-control"
                                           value="{{ old('billing_postal_code', $order->billing_postal_code) }}"
                                           required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Country <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="billing_country"
                                           class="form-control"
                                           value="{{ old('billing_country', $order->billing_country) }}"
                                           required>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Shipping Address --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Shipping Address</h5>
                        </div>
                        <div class="card-body">
                            @if(!$order->same_as_billing)
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Address Line 1</label>
                                        <input type="text"
                                               name="shipping_address_line1"
                                               class="form-control"
                                               value="{{ old('shipping_address_line1', $order->shipping_address_line1) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Address Line 2</label>
                                        <input type="text"
                                               name="shipping_address_line2"
                                               class="form-control"
                                               value="{{ old('shipping_address_line2', $order->shipping_address_line2) }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">City</label>
                                        <input type="text"
                                               name="shipping_city"
                                               class="form-control"
                                               value="{{ old('shipping_city', $order->shipping_city) }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">State/Province</label>
                                        <input type="text"
                                               name="shipping_state"
                                               class="form-control"
                                               value="{{ old('shipping_state', $order->shipping_state) }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Postal Code</label>
                                        <input type="text"
                                               name="shipping_postal_code"
                                               class="form-control"
                                               value="{{ old('shipping_postal_code', $order->shipping_postal_code) }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Country</label>
                                        <input type="text"
                                               name="shipping_country"
                                               class="form-control"
                                               value="{{ old('shipping_country', $order->shipping_country) }}">
                                    </div>
                                </div>
                            @else
                                <p class="text-muted">Shipping address is same as billing address</p>
                            @endif
                        </div>
                    </div>

                    {{-- Order Items (Read Only) --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Order Items</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                Order items cannot be modified after order creation. To change items, cancel this order and create a new one.
                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                            <tr>
                                                <td>{{ $item->product_name }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>${{ number_format($item->price, 2) }}</td>
                                                <td>${{ number_format($item->total, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-active">
                                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                            <td><strong>${{ number_format($order->total, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Additional Information --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Additional Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Payment Method</label>
                                    <input type="text"
                                           name="payment_method"
                                           class="form-control"
                                           value="{{ old('payment_method', $order->payment_method) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Payment Status</label>
                                    <select name="payment_status" class="form-select">
                                        <option value="pending" {{ old('payment_status', $order->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ old('payment_status', $order->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="failed" {{ old('payment_status', $order->payment_status) == 'failed' ? 'selected' : '' }}>Failed</option>
                                        <option value="refunded" {{ old('payment_status', $order->payment_status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                        <option value="partially_refunded" {{ old('payment_status', $order->payment_status) == 'partially_refunded' ? 'selected' : '' }}>Partially Refunded</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tracking Number</label>
                                    <input type="text"
                                           name="tracking_number"
                                           class="form-control"
                                           value="{{ old('tracking_number', $order->tracking_number) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Shipping Method</label>
                                    <input type="text"
                                           name="shipping_method"
                                           class="form-control"
                                           value="{{ old('shipping_method', $order->shipping_method) }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Admin Notes</label>
                                <textarea name="admin_notes"
                                          class="form-control"
                                          rows="4">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Order</button>
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>

            </div>
        </div>
    </main>
@endsection
