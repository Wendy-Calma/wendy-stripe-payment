<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['price_id'])) {
    header('Location: product.php');
    exit;
}

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$base_url = $protocol . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

try {
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price' => $_POST['price_id'],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $base_url . '/success.php',
        'cancel_url' => $base_url . '/cancel.php',
    ]);

    header('Location: ' . $session->url);
    exit;

} catch (Exception $e) {
    echo '<div style="font-family: Arial; padding: 20px; color: red;">';
    echo '<h3>Error creating checkout session</h3>';
    echo '<p>' . $e->getMessage() . '</p>';
    echo '<a href="product.php">Back to Store</a>';
    echo '</div>';
}
?>
