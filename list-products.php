<?php
require 'vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51QKHIqHxWBEKPbZFwuXCxlJrkP3NWc1mZZYRPTrz78DZKef2wlO2Gv2hJDPczSMWG9aUEGb6JNUsT8Cxxqs2JlTz00zR3EZ9zb');

$products = \Stripe\Product::all(['limit' => 10]);
$prices = \Stripe\Price::all(['limit' => 10]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Products</title>
    <style>
        /* Global styles */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom, #eaf8f9, #fdfdfd);
            color: #333;
        }
        h1 {
            text-align: center;
            color: #3a7ca5;
            margin-top: 20px;
            font-size: 2.8em;
            letter-spacing: 1px;
        }
        ul {
            list-style-type: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin: 20px 0;
        }
        li {
            background: #ffffff;
            margin: 15px;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            width: 320px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        li:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
        }
        h2 {
            font-size: 1.6em;
            color: #3a7ca5;
            margin-bottom: 15px;
        }
        img {
            width: 100%;
            height: auto;
            border-radius: 15px;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }
        li:hover img {
            transform: scale(1.05);
        }
        p {
            font-size: 1.4em;
            color: #666;
            margin: 10px 0 0;
        }
        /* Responsive Design */
        @media (max-width: 768px) {
            li {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <h1>Our Products</h1>
    <ul>
        <?php foreach ($products->data as $product): ?>
            <li>
                <h2><?= htmlspecialchars($product->name) ?></h2>
                <img src="<?= htmlspecialchars($product->images[0] ?? 'https://via.placeholder.com/320') ?>" alt="Product Image">
                <p>
                    <?php
                    $price = array_filter($prices->data, fn($p) => $p->product === $product->id);
                    echo '$' . number_format(reset($price)->unit_amount / 100, 2) ?? 'N/A';
                    ?>
                </p>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
