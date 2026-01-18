<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border: 1px solid #ddd;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }

        .company-info h1 {
            font-size: 28px;
            margin-bottom: 10px;
            color: #333;
        }

        .company-info p {
            margin: 2px 0;
            color: #666;
        }

        .invoice-details {
            text-align: right;
        }

        .invoice-details h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }

        .invoice-details p {
            margin: 2px 0;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-paid {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .addresses {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
        }

        .address-block {
            width: 48%;
        }

        .address-block h3 {
            font-size: 14px;
            margin-bottom: 10px;
            color: #666;
            text-transform: uppercase;
        }

        .address-block p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }

        table thead {
            background: #f8f9fa;
        }

        table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }

        table th:last-child,
        table td:last-child {
            text-align: right;
        }

        .totals {
            margin-top: 20px;
            float: right;
            width: 300px;
        }

        .totals table {
            margin: 0;
        }

        .totals td {
            border: none;
            padding: 8px 12px;
        }

        .totals .total-row {
            font-weight: bold;
            font-size: 18px;
            background: #f8f9fa;
        }

        .footer {
            margin-top: 80px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
            clear: both;
        }

        .notes {
            margin: 30px 0;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #333;
        }

        .notes h3 {
            font-size: 14px;
            margin-bottom: 10px;
        }

        @media print {
            body {
                padding: 0;
            }

            .invoice-container {
                border: none;
                padding: 20px;
            }

            .no-print {
                display: none;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .print-button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">Print Invoice</button>

    <div class="invoice-container">
        {{-- Invoice Header --}}
        <div class="invoice-header">
            <div class="company-info">
                <h1>YOUR STORE NAME</h1>
                <p>123 Business Street</p>
                <p>City, State 12345</p>
                <p>Email: info@yourstore.com</p>
                <p>Phone: (123) 456-7890</p>
            </div>
            <div class="invoice-details">
                <h2>INVOICE</h2>
                <p><strong>Invoice #:</strong> {{ $order->order_number }}</p>
                <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
                <p>
                    <strong>Status:</strong>
                    <span class="status-badge status-{{ $order->payment_status }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </p>
            </div>
        </div>

        {{-- Addresses --}}
        <div class="addresses">
            <div class="address-block">
                <h3>Bill To</h3>
                <p><strong>{{ $order->customer_name }}</strong></p>
                <p>{{ $order->billing_address_line1 }}</p>
                @if($order->billing_address_line2)
                    <p>{{ $order->billing_address_line2 }}</p>
                @endif
                <p>{{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_postal_code }}</p>
                <p>{{ $order->billing_country }}</p>
                <p>{{ $order->customer_email }}</p>
                @if($order->customer_phone)
                    <p>{{ $order->customer_phone }}</p>
                @endif
            </div>

            <div class="address-block">
                <h3>Ship To</h3>
                @if($order->same_as_billing)
                    <p><em>Same as billing address</em></p>
                @else
                    <p><strong>{{ $order->customer_name }}</strong></p>
                    <p>{{ $order->shipping_address_line1 }}</p>
                    @if($order->shipping_address_line2)
                        <p>{{ $order->shipping_address_line2 }}</p>
                    @endif
                    <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
                    <p>{{ $order->shipping_country }}</p>
                @endif
            </div>
        </div>

        {{-- Order Items --}}
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>SKU</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Price</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product_name }}</strong>
                            @if($item->variation_details)
                                <br>
                                <small style="color: #666;">
                                    {{ collect($item->variation_details)->implode(' / ') }}
                                </small>
                            @endif
                        </td>
                        <td>{{ $item->product_sku ?? 'N/A' }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">${{ number_format($item->price, 2) }}</td>
                        <td style="text-align: right;">${{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td style="text-align: right;">${{ number_format($order->subtotal, 2) }}</td>
                </tr>
                @if($order->discount_amount > 0)
                    <tr>
                        <td>
                            Discount:
                            @if($order->coupon_code)
                                <br><small>({{ $order->coupon_code }})</small>
                            @endif
                        </td>
                        <td style="text-align: right; color: #dc3545;">-${{ number_format($order->discount_amount, 2) }}</td>
                    </tr>
                @endif
                @if($order->tax_amount > 0)
                    <tr>
                        <td>Tax:</td>
                        <td style="text-align: right;">${{ number_format($order->tax_amount, 2) }}</td>
                    </tr>
                @endif
                @if($order->shipping_cost > 0)
                    <tr>
                        <td>
                            Shipping:
                            @if($order->shipping_method)
                                <br><small>({{ $order->shipping_method }})</small>
                            @endif
                        </td>
                        <td style="text-align: right;">${{ number_format($order->shipping_cost, 2) }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td>TOTAL:</td>
                    <td style="text-align: right;">${{ number_format($order->total, 2) }}</td>
                </tr>
            </table>
        </div>

        <div style="clear: both;"></div>

        {{-- Payment Information --}}
        @if($order->payment_method)
            <div class="notes">
                <h3>Payment Information</h3>
                <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</p>
                @if($order->transaction_id)
                    <p><strong>Transaction ID:</strong> {{ $order->transaction_id }}</p>
                @endif
                <p><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</p>
            </div>
        @endif

        {{-- Shipping Information --}}
        @if($order->tracking_number)
            <div class="notes">
                <h3>Shipping Information</h3>
                <p><strong>Tracking Number:</strong> {{ $order->tracking_number }}</p>
                @if($order->shipped_at)
                    <p><strong>Shipped Date:</strong> {{ $order->shipped_at->format('M d, Y') }}</p>
                @endif
            </div>
        @endif

        {{-- Customer Notes --}}
        @if($order->customer_notes)
            <div class="notes">
                <h3>Customer Notes</h3>
                <p>{{ $order->customer_notes }}</p>
            </div>
        @endif

        {{-- Footer --}}
        <div class="footer">
            <p>Thank you for your business!</p>
            <p>If you have any questions about this invoice, please contact us at info@yourstore.com</p>
            <p style="margin-top: 10px;">
                <strong>YOUR STORE NAME</strong> | www.yourstore.com
            </p>
        </div>
    </div>

    <script>
        // Auto-print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
