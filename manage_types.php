<?php
// manage_types.php
include 'db.php';

// Initialize variables for editing
$edit_mode = false;
$edit_id = '';
$edit_name = '';

// Handle adding a new type
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_type'])) {
    $type_name = $_POST['type_name'];

    if ($conn) {
        $stmt = $conn->prepare("INSERT INTO types (type_name) VALUES (?)");
        $stmt->bind_param("s", $type_name);
        if ($stmt->execute()) {
            echo "<p>Type added successfully!</p>";
        } else {
            echo "<p>Error adding type: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
}

// Handle editing a type
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_type'])) {
    $edit_id = $_POST['edit_id'];
    $edit_name = $_POST['edit_name'];

    if ($conn) {
        $stmt = $conn->prepare("UPDATE types SET type_name = ? WHERE id = ?");
        $stmt->bind_param("si", $edit_name, $edit_id);
        if ($stmt->execute()) {
            echo "<p>Type updated successfully!</p>";
        } else {
            echo "<p>Error updating type: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
}

// Handle loading data for editing
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_mode = true;

    // Fetch the current data of the selected type
    $result = $conn->query("SELECT * FROM types WHERE id = $edit_id");
    if ($result && $result->num_rows > 0) {
        $edit_data = $result->fetch_assoc();
        $edit_name = $edit_data['type_name'];
    }
}

// Fetch all types
$types = $conn->query("SELECT * FROM types");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Types</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Manage Types</h1>

    <!-- Navigation Links -->
    <nav>
        <ul>
            <li><a href="index.php" class="button">Back to Tool Management</a></li>
            <li><a href="manage_persons.php" class="button">Manage Persons</a></li>
            <li><a href="manage_tools.php" class="button">Manage Tools</a></li>
            <li><a href="manage_statuses.php" class="button">Manage Statuses</a></li>
            <li><a href="manage_locations.php" class="button">Manage Locations</a></li>
        </ul>
    </nav>

    <!-- Form to add or edit a type -->
    <form method="POST">
        <label for="type_name">Type Name:</label>
        <input type="text" id="type_name" name="<?= $edit_mode ? 'edit_name' : 'type_name' ?>" value="<?= htmlspecialchars($edit_name) ?>" required>
        <?php if ($edit_mode): ?>
            <input type="hidden" name="edit_id" value="<?= $edit_id ?>">
            <button type="submit" name="edit_type">Update Type</button>
            <a href="manage_types.php">Cancel</a>
        <?php else: ?>
            <button type="submit" name="add_type">Add Type</button>
        <?php endif; ?>
    </form>

    <!-- Display all types -->
    <h2>Existing Types</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Type Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($types && $types->num_rows > 0): ?>
                <?php while ($type = $types->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($type['id']) ?></td>
                        <td><?= htmlspecialchars($type['type_name']) ?></td>
                        <td>
                            <a href="manage_types.php?edit=<?= $type['id'] ?>">Edit</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No types found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
