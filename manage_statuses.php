<?php
// manage_statuses.php
include 'db.php';

// Initialize variables for editing
$edit_mode = false;
$edit_id = '';
$edit_name = '';

// Handle adding a new status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_status'])) {
    $status_name = $_POST['status_name'];

    if ($conn) {
        $stmt = $conn->prepare("INSERT INTO statuses (status_name) VALUES (?)");
        $stmt->bind_param("s", $status_name);
        if ($stmt->execute()) {
            echo "<p>Status added successfully!</p>";
        } else {
            echo "<p>Error adding status: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
}

// Handle editing a status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_status'])) {
    $edit_id = $_POST['edit_id'];
    $edit_name = $_POST['edit_name'];

    if ($conn) {
        $stmt = $conn->prepare("UPDATE statuses SET status_name = ? WHERE id = ?");
        $stmt->bind_param("si", $edit_name, $edit_id);
        if ($stmt->execute()) {
            echo "<p>Status updated successfully!</p>";
        } else {
            echo "<p>Error updating status: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
}

// Handle loading data for editing
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_mode = true;

    // Fetch the current data of the selected status
    $result = $conn->query("SELECT * FROM statuses WHERE id = $edit_id");
    if ($result && $result->num_rows > 0) {
        $edit_data = $result->fetch_assoc();
        $edit_name = $edit_data['status_name'];
    }
}

// Fetch all statuses
$statuses = $conn->query("SELECT * FROM statuses");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Statuses</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Manage Statuses</h1>

    <!-- Navigation Links -->
    <nav>
        <ul>
            <li><a href="index.php" class="button">Back to Tool Management</a></li>
            <li><a href="manage_persons.php" class="button">Manage Persons</a></li>
            <li><a href="manage_tools.php" class="button">Manage Tools</a></li>
            <li><a href="manage_types.php" class="button">Manage Types</a></li>
            <li><a href="manage_locations.php" class="button">Manage Locations</a></li>
        </ul>
    </nav>

    <!-- Form to add or edit a status -->
    <form method="POST">
        <label for="status_name">Status Name:</label>
        <input type="text" id="status_name" name="<?= $edit_mode ? 'edit_name' : 'status_name' ?>" value="<?= htmlspecialchars($edit_name) ?>" required>
        <?php if ($edit_mode): ?>
            <input type="hidden" name="edit_id" value="<?= $edit_id ?>">
            <button type="submit" name="edit_status">Update Status</button>
            <a href="manage_statuses.php">Cancel</a>
        <?php else: ?>
            <button type="submit" name="add_status">Add Status</button>
        <?php endif; ?>
    </form>

    <!-- Display all statuses -->
    <h2>Existing Statuses</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Status Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($statuses && $statuses->num_rows > 0): ?>
                <?php while ($status = $statuses->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($status['id']) ?></td>
                        <td><?= htmlspecialchars($status['status_name']) ?></td>
                        <td>
                            <a href="manage_statuses.php?edit=<?= $status['id'] ?>">Edit</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No statuses found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
