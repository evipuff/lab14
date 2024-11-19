<?php
require 'vendor/autoload.php';
\Stripe\Stripe::setApiKey('sk_test_51QKHIqHxWBEKPbZFwuXCxlJrkP3NWc1mZZYRPTrz78DZKef2wlO2Gv2hJDPczSMWG9aUEGb6JNUsT8Cxxqs2JlTz00zR3EZ9zb');

// Fetch customers and products
$customers = \Stripe\Customer::all(['limit' => 10]);
$products = \Stripe\Product::all(['limit' => 10]);
$prices = \Stripe\Price::all(['limit' => 10]);

$invoiceCreated = false; // Flag to track if the invoice is created

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId = $_POST['customer'];
    $selectedProducts = $_POST['products'];

    // Create InvoiceItems before creating the invoice
    foreach ($selectedProducts as $productId) {
        // Find the price for each selected product
        $price = array_filter($prices->data, fn($p) => $p->product === $productId);
        if ($price) {
            $priceId = reset($price)->id;

            // Create an invoice item for each selected product
            \Stripe\InvoiceItem::create([
                'customer' => $customerId,
                'price' => $priceId,
                'quantity' => 1,
            ]);
        }
    }

    // Now, create the invoice (without the 'lines' parameter)
    $invoice = \Stripe\Invoice::create([
        'customer' => $customerId,
        'auto_advance' => true, // Automatically finalize the invoice
    ]);

    // Finalize the invoice
    $invoice->finalizeInvoice();

    // Prepare invoice data
    $invoiceId = $invoice->id;
    $invoicePdfUrl = $invoice->invoice_pdf; // Invoice PDF URL
    $hostedInvoiceUrl = $invoice->hosted_invoice_url; // Hosted invoice URL for payment

    $invoiceCreated = true; // Set the flag to true to display the notification
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Invoice</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #f7f9fc;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        select,
        button {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            font-size: 1em;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-sizing: border-box;
        }

        select:focus,
        button:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
        }

        button {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        .product-list {
            text-align: left;
            margin-bottom: 20px;
            padding: 0;
        }

        .product-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }

        .product-item:hover {
            background-color: #f1f1f1;
        }

        .selected {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;  /* Bold text on selection */
        }

        .product-item label {
            font-size: 1em;
            color: #333;
        }

        /* Notification style */
        .notification {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .form-container {
                width: 80%;
            }
        }
    </style>
</head>
<body>

    <div class="form-container">
        <?php if ($invoiceCreated): ?>
            <div class="notification">
                Invoice Created!
            </div>
        <?php endif; ?>
        
        <h1>Create Invoice</h1>
        
        <form method="POST">
            <!-- Customer Dropdown -->
            <select name="customer" required>
                <?php foreach ($customers->data as $customer): ?>
                    <option value="<?= $customer->id ?>"><?= $customer->name ?></option>
                <?php endforeach; ?>
            </select>

            <!-- Products List as Clickable Product Names -->
            <div class="product-list">
                <?php foreach ($products->data as $product): ?>
                    <div class="product-item" data-product-id="<?= $product->id ?>">
                        <label><?= $product->name ?></label>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Hidden Input to Store Selected Products -->
            <input type="hidden" name="products[]" id="selectedProducts">

            <!-- Submit Button -->
            <button type="submit">Create Invoice</button>
        </form>

        <?php if (isset($invoiceId)): ?>
            <!-- Buttons for Invoice PDF Download and Payment -->
            <div class="invoice-links">
                <a href="<?= $invoicePdfUrl ?>" target="_blank">
                    <button type="button">Download Invoice PDF</button>
                </a>
                <br><br>
                <a href="<?= $hostedInvoiceUrl ?>" target="_blank">
                    <button type="button">Go to Payment</button>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Array to store selected product IDs
        let selectedProducts = [];

        // Get all product items
        const productItems = document.querySelectorAll('.product-item');

        // Add click event listeners to product items
        productItems.forEach(item => {
            item.addEventListener('click', () => {
                const productId = item.getAttribute('data-product-id');
                const isSelected = item.classList.contains('selected');

                // Toggle selected class and update selectedProducts array
                if (isSelected) {
                    item.classList.remove('selected');
                    selectedProducts = selectedProducts.filter(id => id !== productId);
                } else {
                    item.classList.add('selected');
                    selectedProducts.push(productId);
                }

                // Update hidden input field with selected product IDs
                document.getElementById('selectedProducts').value = selectedProducts.join(',');
            });
        });
    </script>

</body>
</html>
