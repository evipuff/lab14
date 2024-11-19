<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'vendor/autoload.php';
    \Stripe\Stripe::setApiKey('sk_test_51QKHIqHxWBEKPbZFwuXCxlJrkP3NWc1mZZYRPTrz78DZKef2wlO2Gv2hJDPczSMWG9aUEGb6JNUsT8Cxxqs2JlTz00zR3EZ9zb');

    // Collect customer information
    $customer = \Stripe\Customer::create([
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'address' => [
            'line1' => $_POST['address_line1'],
            'city' => $_POST['city'],
            'state' => $_POST['state'],
            'postal_code' => $_POST['postal_code'],
            'country' => $_POST['country']
        ]
    ]);

    // Set flag to indicate form submission is complete
    $formSubmitted = true;
    $customerId = $customer->id;  // Customer ID for notification
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Customer</title>
    <style>
        /* Global Styles */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom, #eaf8f9, #fdfdfd);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding-top: 20px;
            flex-direction: column;
        }

        /* Success Notification */
        .notification {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            font-size: 1.2em;
            text-align: center;
            width: 80%;
            max-width: 400px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: none; /* Hidden initially */
        }

        /* Form Styles */
        form {
            background: #ffffff;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 400px; /* Fixed form width */
            text-align: center;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        form.disabled {
            opacity: 0.6; /* Disable the form after submission */
            pointer-events: none;
        }

        h1 {
            font-size: 1.6em;
            margin-bottom: 20px;
            color: #3a7ca5;
        }

        /* Input & Button Styles */
        input[type="text"],
        input[type="email"],
        button {
            display: block;
            width: 90%;
            margin: 10px auto;
            padding: 10px;
            font-size: 0.95em;
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: border 0.3s ease, box-shadow 0.3s ease;
            box-sizing: border-box;
        }

        input[type="text"]:focus,
        input[type="email"]:focus {
            border: 1px solid #3a7ca5;
            box-shadow: 0 0 5px rgba(58, 124, 165, 0.5);
            outline: none;
        }

        button {
            background: #3a7ca5;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 1em;
            font-weight: bold;
            padding: 10px;
            border-radius: 8px;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background: #2e90c1;
            transform: translateY(-2px);
        }

        button:active {
            transform: translateY(1px);
        }

        /* Placeholder Styling */
        input::placeholder {
            color: #888;
        }

        /* Tooltip Styles */
        .tooltip {
            position: relative;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: 200px;
            background-color: #3a7ca5;
            color: #fff;
            text-align: center;
            border-radius: 5px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 100%;
            left: 50%;
            margin-left: -100px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            form {
                width: 80%; /* Slightly smaller form width on smaller screens */
            }

            .notification {
                width: 90%;
            }
        }
    </style>
</head>
<body>

    <!-- Success Notification -->
    <?php if (isset($formSubmitted) && $formSubmitted): ?>
        <div class="notification" id="successNotification">
            Customer created successfully!
        </div>
    <?php endif; ?>

    <!-- Form -->
    <form method="POST" class="<?= isset($formSubmitted) && $formSubmitted ? 'disabled' : ''; ?>">
        <h1>Create Customer</h1>
        
        <!-- Name Input -->
        <div class="tooltip">
            <input type="text" name="name" placeholder="Full Name" required <?= isset($formSubmitted) && $formSubmitted ? 'disabled' : ''; ?>>
            <span class="tooltiptext">Enter your full name</span>
        </div>
        
        <!-- Email Input -->
        <div class="tooltip">
            <input type="email" name="email" placeholder="Email Address" required <?= isset($formSubmitted) && $formSubmitted ? 'disabled' : ''; ?>>
            <span class="tooltiptext">Enter a valid email</span>
        </div>

        <!-- Phone Input -->
        <div class="tooltip">
            <input type="text" name="phone" placeholder="Phone Number" required <?= isset($formSubmitted) && $formSubmitted ? 'disabled' : ''; ?>>
            <span class="tooltiptext">Enter your phone number</span>
        </div>

        <!-- Address Line 1 Input -->
        <div class="tooltip">
            <input type="text" name="address_line1" placeholder="Address Line 1" required <?= isset($formSubmitted) && $formSubmitted ? 'disabled' : ''; ?>>
            <span class="tooltiptext">Enter your street address</span>
        </div>

        <!-- City Input -->
        <div class="tooltip">
            <input type="text" name="city" placeholder="City" required <?= isset($formSubmitted) && $formSubmitted ? 'disabled' : ''; ?>>
            <span class="tooltiptext">Enter your city</span>
        </div>

        <!-- State Input -->
        <div class="tooltip">
            <input type="text" name="state" placeholder="State" required <?= isset($formSubmitted) && $formSubmitted ? 'disabled' : ''; ?>>
            <span class="tooltiptext">Enter your state</span>
        </div>

        <!-- Postal Code Input -->
        <div class="tooltip">
            <input type="text" name="postal_code" placeholder="Postal Code" required <?= isset($formSubmitted) && $formSubmitted ? 'disabled' : ''; ?>>
            <span class="tooltiptext">Enter your postal code</span>
        </div>

        <!-- Country Input -->
        <div class="tooltip">
            <input type="text" name="country" placeholder="Country" required <?= isset($formSubmitted) && $formSubmitted ? 'disabled' : ''; ?>>
            <span class="tooltiptext">Enter your country</span>
        </div>

        <!-- Submit Button -->
        <button type="submit" <?= isset($formSubmitted) && $formSubmitted ? 'disabled' : ''; ?>>Create Customer</button>
    </form>

    <script>
        // Show success notification after form submission
        <?php if (isset($formSubmitted) && $formSubmitted): ?>
            document.getElementById('successNotification').style.display = 'block';
        <?php endif; ?>
    </script>
