<?php
// manage_locations.php
include 'db.php';

// Initialize variables for editing
$edit_mode = false;
$edit_id = '';
$edit_name = '';

// Handle adding a new location
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_location'])) {
    $location_name = $_POST['location_name'];

    if ($conn) {
        $stmt = $conn->prepare("INSERT INTO locations (location_name) VALUES (?)");
        $stmt->bind_param("s", $location_name);
        if ($stmt->execute()) {
            echo "<p>Location added successfully!</p>";
        } else {
            echo "<p>Error adding location: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
}

// Handle editing a location
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_location'])) {
    $edit_id = $_POST['edit_id'];
    $edit_name = $_POST['edit_name'];

    if ($conn) {
        $stmt = $conn->prepare("UPDATE locations SET location_name = ? WHERE id = ?");
        $stmt->bind_param("si", $edit_name, $edit_id);
        if ($stmt->execute()) {
            echo "<p>Location updated successfully!</p>";
        } else {
            echo "<p>Error updating location: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
}

// Handle loading data for editing
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_mode = true;

    // Fetch the current data of the selected location
    $result = $conn->query("SELECT * FROM locations WHERE id = $edit_id");
    if ($result && $result->num_rows > 0) {
        $edit_data = $result->fetch_assoc();
        $edit_name = $edit_data['location_name'];
    }
}

// Fetch all locations
$locations = $conn->query("SELECT * FROM locations");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Locations</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Manage Locations</h1>

    <!-- Navigation Links -->
    <nav>
        <ul>
            <li><a href="index.php" class="button">Back to Tool Management</a></li>
            <li><a href="manage_persons.php" class="button">Manage Persons</a></li>
            <li><a href="manage_tools.php" class="button">Manage Tools</a></li>
            <li><a href="manage_types.php" class="button">Manage Types</a></li>
            <li><a href="manage_statuses.php" class="button">Manage Statuses</a></li>
        </ul>
    </nav>

    <!-- Form to add or edit a location -->
    <form method="POST">
        <label for="location_name">Location Name:</label>
        <input type="text" id="location_name" name="<?= $edit_mode ? 'edit_name' : 'location_name' ?>" value="<?= htmlspecialchars($edit_name) ?>" required>
        <?php if ($edit_mode): ?>
            <input type="hidden" name="edit_id" value="<?= $edit_id ?>">
            <button type="submit" name="edit_location">Update Location</button>
            <a href="manage_locations.php">Cancel</a>
        <?php else: ?>
            <button type="submit" name="add_location">Add Location</button>
        <?php endif; ?>
    </form>

    <!-- Display all locations -->
    <h2>Existing Locations</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Location Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($locations && $locations->num_rows > 0): ?>
                <?php while ($location = $locations->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($location['id']) ?></td>
                        <td><?= htmlspecialchars($location['location_name']) ?></td>
                        <td>
                            <a href="manage_locations.php?edit=<?= $location['id'] ?>">Edit</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No locations found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
