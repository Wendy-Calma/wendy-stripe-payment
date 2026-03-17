<?php
require 'config.php';

$products = [];
$error = null;

try {
    $productsData = \Stripe\Product::all([
        'active' => true,
        'expand' => ['data.default_price']
    ]);
    $products = $productsData->data;
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h1 class="text-center mb-5">Our Products</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger" role="alert">
            Error fetching products: <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($products as $product): ?>
            <?php 
                if (!isset($product->default_price)) continue; 
                
                $price_id = $product->default_price->id;
                $amount = number_format($product->default_price->unit_amount / 100, 2);
                $currency = strtoupper($product->default_price->currency);
            ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($product->images)): ?>
                        <img src="<?php echo $product->images[0]; ?>" class="card-img-top" alt="Product Image" style="height: 450px;">
                    <?php else: ?>
                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center card-img-top" style="height: 200px;">
                            <span>No Image</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($product->name); ?></h5>
                        <p class="card-text text-muted"><?php echo htmlspecialchars($product->description ?? 'No description available.'); ?></p>
                        <h4 class="mt-auto mb-3"><?php echo $amount . ' ' . $currency; ?></h4>
                        
                        <form method="POST" action="checkout.php">
                            <input type="hidden" name="price_id" value="<?php echo $price_id; ?>">
                            <button type="submit" class="btn btn-primary w-100">Buy Now</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
