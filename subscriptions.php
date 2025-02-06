<?php 
include 'includes/header.php';
include 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Add new subscription
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $start_date = trim($_POST['start_date']);
    $renewal_date = trim($_POST['renewal_date']);
    
    $stmt = $pdo->prepare("INSERT INTO subscriptions 
        (user_id, name, price, start_date, renewal_date) 
        VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'],
        $name,
        $price,
        $start_date,
        $renewal_date
    ]);
}

// Get user's subscriptions
$stmt = $pdo->prepare("SELECT * FROM subscriptions WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$subscriptions = $stmt->fetchAll();
?>

<div class="container">
    <h2>Manage Subscriptions</h2>
    
    <!-- Add Subscription Form -->
    <div class="form-container">
        <h3>Add New Subscription</h3>
        <form method="post">
            <div class="form-group">
                <label>Service Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Price</label>
                <input type="number" step="0.01" name="price" required>
            </div>
            <div class="form-group">
                <label>Start Date</label>
                <input type="date" name="start_date" required>
            </div>
            <div class="form-group">
                <label>Renewal Date</label>
                <input type="date" name="renewal_date" required>
            </div>
            <button type="submit" name="add" class="btn">Add Subscription</button>
        </form>
    </div>

    <!-- Subscriptions List -->
    <table class="subscription-table">
        <thead>
            <tr>
                <th>Service</th>
                <th>Price</th>
                <th>Start Date</th>
                <th>Renewal Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($subscriptions as $sub): ?>
            <tr>
                <td><?= htmlspecialchars($sub['name']) ?></td>
                <td>$<?= number_format($sub['price'], 2) ?></td>
                <td><?= $sub['start_date'] ?></td>
                <td><?= $sub['renewal_date'] ?></td>
                <td><?= ucfirst($sub['status']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>