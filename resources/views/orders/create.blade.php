@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title mb-4">Create New Order</h4>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('orders.store') }}" id="orderForm">
                    @csrf

                    {{-- Customer Information --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Customer Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Select Existing Customer (Optional)</label>
                                    <select id="userSelect" class="form-select">
                                        <option value="">-- New Customer --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}"
                                                    data-name="{{ $user->name }}"
                                                    data-email="{{ $user->email }}">
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="user_id" id="user_id">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_name" id="customer_name" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="customer_email" id="customer_email" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="customer_phone" class="form-control">
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
                                    <input type="text" name="billing_address_line1" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Address Line 2</label>
                                    <input type="text" name="billing_address_line2" class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">City <span class="text-danger">*</span></label>
                                    <input type="text" name="billing_city" class="form-control" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">State/Province</label>
                                    <input type="text" name="billing_state" class="form-control">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Postal Code <span class="text-danger">*</span></label>
                                    <input type="text" name="billing_postal_code" class="form-control" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Country <span class="text-danger">*</span></label>
                                    <input type="text" name="billing_country" class="form-control" value="USA" required>
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
                            <div class="form-check mb-3">
                                <input type="hidden" name="same_as_billing" value="0">
                                <input type="checkbox" name="same_as_billing" class="form-check-input" id="same_as_billing" value="1" checked>
                                <label class="form-check-label" for="same_as_billing">
                                    Same as billing address
                                </label>
                            </div>

                            <div id="shipping_address_fields" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Address Line 1</label>
                                        <input type="text" name="shipping_address_line1" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Address Line 2</label>
                                        <input type="text" name="shipping_address_line2" class="form-control">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">City</label>
                                        <input type="text" name="shipping_city" class="form-control">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">State/Province</label>
                                        <input type="text" name="shipping_state" class="form-control">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Postal Code</label>
                                        <input type="text" name="shipping_postal_code" class="form-control">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Country</label>
                                        <input type="text" name="shipping_country" class="form-control" value="USA">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Order Items --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Order Items</h5>
                        </div>
                        <div class="card-body">
                            <table class="table" id="items-table">
                                <thead>
                                    <tr>
                                        <th width="35%">Product</th>
                                        <th width="25%">Variation</th>
                                        <th width="10%">Qty</th>
                                        <th width="12%">Price</th>
                                        <th width="13%">Subtotal</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="items-container">
                                    <!-- Items will be added here -->
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-primary btn-sm" id="add-item">Add Item</button>
                        </div>
                    </div>

                    {{-- Order Totals --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tax Amount</label>
                                        <input type="number" name="tax_amount" id="tax_amount" class="form-control" step="0.01" value="0">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Shipping Cost</label>
                                        <input type="number" name="shipping_cost" id="shipping_cost" class="form-control" step="0.01" value="0">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Discount Amount</label>
                                        <input type="number" name="discount_amount" id="discount_amount" class="form-control" step="0.01" value="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <table class="table">
                                        <tr>
                                            <td>Subtotal:</td>
                                            <td class="text-end"><strong id="display_subtotal">$0.00</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Tax:</td>
                                            <td class="text-end"><strong id="display_tax">$0.00</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Shipping:</td>
                                            <td class="text-end"><strong id="display_shipping">$0.00</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Discount:</td>
                                            <td class="text-end"><strong id="display_discount">$0.00</strong></td>
                                        </tr>
                                        <tr class="table-active">
                                            <td><h5>Total:</h5></td>
                                            <td class="text-end"><h5 id="display_total">$0.00</h5></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Order Details --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Order Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Order Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select" required>
                                        <option value="pending">Pending</option>
                                        <option value="processing">Processing</option>
                                        <option value="shipped">Shipped</option>
                                        <option value="delivered">Delivered</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Payment Status <span class="text-danger">*</span></label>
                                    <select name="payment_status" class="form-select" required>
                                        <option value="pending">Pending</option>
                                        <option value="paid">Paid</option>
                                        <option value="failed">Failed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Payment Method</label>
                                    <select name="payment_method" class="form-select">
                                        <option value="">-- Select --</option>
                                        <option value="credit_card">Credit Card</option>
                                        <option value="debit_card">Debit Card</option>
                                        <option value="paypal">PayPal</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="cash">Cash</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Shipping Method</label>
                                    <input type="text" name="shipping_method" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Create Order</button>
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>

            </div>
        </div>
    </main>

    {{-- Item Row Template --}}
    <template id="item-template">
        <tr class="item-row">
            <td>
                <select name="items[INDEX][product_id]" class="form-select product-select" required>
                    <option value="">-- Select Product --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}"
                                data-price="{{ $product->regular_price }}"
                                data-type="{{ $product->product_type }}"
                                data-sku="{{ $product->sku }}">
                            {{ $product->name }}
                            @if($product->product_type === 'simple')
                                - ${{ number_format($product->regular_price, 2) }}
                            @else
                                (Variable)
                            @endif
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="items[INDEX][product_variation_id]" class="form-select variation-select" style="display:none;">
                    <option value="">-- Select Variation --</option>
                </select>
                <span class="no-variation text-muted">N/A</span>
            </td>
            <td>
                <input type="number" name="items[INDEX][quantity]" class="form-control item-quantity" min="1" value="1" required>
            </td>
            <td>
                <input type="number" name="items[INDEX][price]" class="form-control item-price" step="0.01" required readonly>
            </td>
            <td>
                <input type="text" class="form-control item-subtotal" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-item">×</button>
            </td>
        </tr>
    </template>

    <script>
        let itemIndex = 0;

        // Product variations data (from backend)
        const productVariations = @json($productVariationsData);

        // User select
        document.getElementById('userSelect').addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            if (option.value) {
                document.getElementById('user_id').value = option.value;
                document.getElementById('customer_name').value = option.dataset.name;
                document.getElementById('customer_email').value = option.dataset.email;
            } else {
                document.getElementById('user_id').value = '';
                document.getElementById('customer_name').value = '';
                document.getElementById('customer_email').value = '';
            }
        });

        // Same as billing toggle
        document.getElementById('same_as_billing').addEventListener('change', function() {
            document.getElementById('shipping_address_fields').style.display = this.checked ? 'none' : 'block';
        });

        // Add item
        document.getElementById('add-item').addEventListener('click', function() {
            const template = document.getElementById('item-template');
            const clone = template.content.cloneNode(true);

            // Replace INDEX with actual index
            clone.querySelectorAll('[name*="INDEX"]').forEach(el => {
                el.name = el.name.replace('INDEX', itemIndex);
            });

            document.getElementById('items-container').appendChild(clone);
            attachItemEventListeners();
            itemIndex++;
        });

        // Attach event listeners to items
        function attachItemEventListeners() {
            // Product select
            document.querySelectorAll('.product-select').forEach(select => {
                select.removeEventListener('change', handleProductChange);
                select.addEventListener('change', handleProductChange);
            });

            // Variation select
            document.querySelectorAll('.variation-select').forEach(select => {
                select.removeEventListener('change', handleVariationChange);
                select.addEventListener('change', handleVariationChange);
            });

            // Quantity change
            document.querySelectorAll('.item-quantity').forEach(input => {
                input.removeEventListener('input', calculateItemSubtotal);
                input.addEventListener('input', calculateItemSubtotal);
            });

            // Remove item
            document.querySelectorAll('.remove-item').forEach(btn => {
                btn.removeEventListener('click', handleRemoveItem);
                btn.addEventListener('click', handleRemoveItem);
            });
        }

        function handleProductChange(e) {
            const option = e.target.options[e.target.selectedIndex];
            const row = e.target.closest('.item-row');
            const variationSelect = row.querySelector('.variation-select');
            const noVariationSpan = row.querySelector('.no-variation');
            const priceInput = row.querySelector('.item-price');
            const productType = option.dataset.type;
            const productId = option.value;

            // Clear variation select
            variationSelect.innerHTML = '<option value="">-- Select Variation --</option>';

            if (productType === 'variable' && productId) {
                // Show variation dropdown
                variationSelect.style.display = 'block';
                noVariationSpan.style.display = 'none';
                priceInput.value = '';

                // Load variations
                const variations = productVariations[productId] || [];
                variations.forEach(variation => {
                    const opt = document.createElement('option');
                    opt.value = variation.id;
                    opt.textContent = `${variation.label} - $${parseFloat(variation.price).toFixed(2)}`;
                    opt.dataset.price = variation.price;
                    opt.dataset.sku = variation.sku;
                    if (variation.stock <= 0) {
                        opt.textContent += ' (Out of Stock)';
                        opt.disabled = true;
                    }
                    variationSelect.appendChild(opt);
                });
            } else if (productType === 'simple' && productId) {
                // Hide variation dropdown
                variationSelect.style.display = 'none';
                noVariationSpan.style.display = 'inline';
                priceInput.value = option.dataset.price;
                calculateItemSubtotal.call(priceInput);
            } else {
                // No product selected
                variationSelect.style.display = 'none';
                noVariationSpan.style.display = 'inline';
                priceInput.value = '';
            }
        }

        function handleVariationChange(e) {
            const option = e.target.options[e.target.selectedIndex];
            const row = e.target.closest('.item-row');
            const priceInput = row.querySelector('.item-price');

            if (option.value) {
                priceInput.value = option.dataset.price;
                calculateItemSubtotal.call(priceInput);
            } else {
                priceInput.value = '';
                calculateItemSubtotal.call(priceInput);
            }
        }

        function calculateItemSubtotal() {
            const row = this.closest('.item-row');
            const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const subtotal = quantity * price;

            row.querySelector('.item-subtotal').value = '$' + subtotal.toFixed(2);
            calculateTotals();
        }

        function handleRemoveItem() {
            this.closest('.item-row').remove();
            calculateTotals();
        }

        function calculateTotals() {
            let subtotal = 0;

            document.querySelectorAll('.item-row').forEach(row => {
                const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
                const price = parseFloat(row.querySelector('.item-price').value) || 0;
                subtotal += quantity * price;
            });

            const tax = parseFloat(document.getElementById('tax_amount').value) || 0;
            const shipping = parseFloat(document.getElementById('shipping_cost').value) || 0;
            const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
            const total = subtotal + tax + shipping - discount;

            document.getElementById('display_subtotal').textContent = '$' + subtotal.toFixed(2);
            document.getElementById('display_tax').textContent = '$' + tax.toFixed(2);
            document.getElementById('display_shipping').textContent = '$' + shipping.toFixed(2);
            document.getElementById('display_discount').textContent = '$' + discount.toFixed(2);
            document.getElementById('display_total').textContent = '$' + total.toFixed(2);
        }

        // Listen to tax, shipping, discount changes
        ['tax_amount', 'shipping_cost', 'discount_amount'].forEach(id => {
            document.getElementById(id).addEventListener('input', calculateTotals);
        });

        // Add first item on load
        document.getElementById('add-item').click();
    </script>
@endsection
