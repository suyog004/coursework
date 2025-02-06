<?php
session_start();
include 'config.php'; // Database connection
include 'header.php';

// Fetch subscriptions from database
try {
    $stmt = $pdo->prepare("SELECT * FROM subscriptions");
    $stmt->execute();
    $subscriptions = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error fetching subscriptions: " . $e->getMessage();
}

// Delete subscription if requested
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM subscriptions WHERE id = ?");
        $stmt->execute([$delete_id]);
        header("Location: dashboard.php"); // Redirect after deletion
    } catch (PDOException $e) {
        echo "Error deleting subscription: " . $e->getMessage();
    }
}

// Add new subscription
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_subscription'])) {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $start_date = trim($_POST['start_date']);
    $renewal_date = trim($_POST['renewal_date']);
    $status = trim($_POST['status']);

    try {
        $stmt = $pdo->prepare("INSERT INTO subscriptions (name, price, start_date, renewal_date, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $price, $start_date, $renewal_date, $status]);
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        echo "Error adding subscription: " . $e->getMessage();
    }
}

// Edit subscription
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_subscription'])) {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $start_date = trim($_POST['start_date']);
    $renewal_date = trim($_POST['renewal_date']);
    $status = trim($_POST['status']);

    try {
        $stmt = $pdo->prepare("UPDATE subscriptions SET name = ?, price = ?, start_date = ?, renewal_date = ?, status = ? WHERE id = ?");
        $stmt->execute([$name, $price, $start_date, $renewal_date, $status, $id]);
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        echo "Error updating subscription: " . $e->getMessage();
    }
}
?>
<html>
    <head>
    <link rel="stylesheet" type="text/css" href="sttt.css">
</head>
<!-- HTML Content for Dashboard -->
<div class="dashboard-container">
    <h2>Manage Subscriptions</h2>

    <!-- Add Subscription Form -->
    <button onclick="document.getElementById('addModal').style.display='block'" class="btn">Add Subscription</button>

    <!-- Subscription Overview Table -->
    <h3>Subscription Overview</h3>
    <table class="subscription-table">
        <thead>
            <tr>
                <th>Subscription Name</th>
                <th>Price</th>
                <th>Start Date</th>
                <th>Renewal Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($subscriptions as $subscription): ?>
                <tr>
                    <td><?php echo $subscription['name']; ?></td>
                    <td><?php echo '$' . number_format($subscription['price'], 2); ?></td>
                    <td><?php echo $subscription['start_date']; ?></td>
                    <td><?php echo $subscription['renewal_date']; ?></td>
                    <td><?php echo ucfirst($subscription['status']); ?></td>
                    <td>
                        <button onclick="editSubscription(<?php echo $subscription['id']; ?>, '<?php echo $subscription['name']; ?>', <?php echo $subscription['price']; ?>, '<?php echo $subscription['start_date']; ?>', '<?php echo $subscription['renewal_date']; ?>', '<?php echo $subscription['status']; ?>')" class="btn">Edit</button>
                        <a href="?delete_id=<?php echo $subscription['id']; ?>" onclick="return confirm('Are you sure?')" class="btn">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Subscription Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('addModal').style.display='none'">&times;</span>
        <h3>Add New Subscription</h3>
        <form action="dashboard.php" method="post">
            <input type="hidden" name="add_subscription" value="1">
            <label for="name">Subscription Name</label>
            <input type="text" id="name" name="name" required>
            <label for="price">Price</label>
            <input type="number" id="price" name="price" step="0.01" required>
            <label for="start_date">Start Date</label>
            <input type="date" id="start_date" name="start_date" required>
            <label for="renewal_date">Renewal Date</label>
            <input type="date" id="renewal_date" name="renewal_date" required>
            <label for="status">Status</label>
            <select id="status" name="status" required>
                <option value="active">Active</option>
                <option value="canceled">Canceled</option>
            </select>
            <button type="submit" class="btn">Add Subscription</button>
        </form>
    </div>
</div>

<!-- Edit Subscription Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('editModal').style.display='none'">&times;</span>
        <h3>Edit Subscription</h3>
        <form action="dashboard.php" method="post">
            <input type="hidden" name="edit_subscription" value="1">
            <input type="hidden" name="id" id="edit_id">
            <label for="edit_name">Subscription Name</label>
            <input type="text" id="edit_name" name="name" required>
            <label for="edit_price">Price</label>
            <input type="number" id="edit_price" name="price" step="0.01" required>
            <label for="edit_start_date">Start Date</label>
            <input type="date" id="edit_start_date" name="start_date" required>
            <label for="edit_renewal_date">Renewal Date</label>
            <input type="date" id="edit_renewal_date" name="renewal_date" required>
            <label for="edit_status">Status</label>
            <select id="edit_status" name="status" required>
                <option value="active">Active</option>
                <option value="canceled">Canceled</option>
            </select>
            <button type="submit" class="btn">Update Subscription</button>
        </form>
    </div>
</div>

<!-- Modal and JavaScript for Edit Subscription -->
<script>
    function editSubscription(id, name, price, start_date, renewal_date, status) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_price').value = price;
        document.getElementById('edit_start_date').value = start_date;
        document.getElementById('edit_renewal_date').value = renewal_date;
        document.getElementById('edit_status').value = status;
        document.getElementById('editModal').style.display = 'block';
    }

    // Close modal if clicked outside
    window.onclick = function(event) {
        if (event.target == document.getElementById('addModal') || event.target == document.getElementById('editModal')) {
            document.getElementById('addModal').style.display = 'none';
            document.getElementById('editModal').style.display = 'none';
        }
    }
</script>

<?php include 'footer.php'; ?>

<!-- CSS Styling for Modals -->
<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        padding-top: 60px;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>
</html>