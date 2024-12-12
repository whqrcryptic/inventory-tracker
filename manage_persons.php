<?php
// manage_persons.php
include 'db.php';

// Initialize variables for editing
$edit_mode = false;
$edit_id = '';
$edit_name = '';

// Handle adding a new person
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_person'])) {
    $name = isset($_POST['name']) ? $_POST['name'] : '';

    if ($conn && !empty($name)) {
        $stmt = $conn->prepare("INSERT INTO persons (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            echo "<p>Person added successfully!</p>";
        } else {
            echo "<p>Error adding person: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p>Please provide a name for the person.</p>";
    }
}

// Handle editing a person
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_person'])) {
    $edit_id = isset($_POST['edit_id']) ? $_POST['edit_id'] : '';
    $edit_name = isset($_POST['edit_name']) ? $_POST['edit_name'] : '';

    if ($conn && !empty($edit_name) && !empty($edit_id)) {
        $stmt = $conn->prepare("UPDATE persons SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $edit_name, $edit_id);
        if ($stmt->execute()) {
            echo "<p>Person updated successfully!</p>";
        } else {
            echo "<p>Error updating person: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p>Please provide a name and valid person ID to update.</p>";
    }
}

// Handle loading data for editing
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];

    // Validate the edit ID to ensure it's a valid integer
    if (is_numeric($edit_id)) {
        $edit_mode = true;

        // Fetch the current data of the selected person
        $stmt = $conn->prepare("SELECT * FROM persons WHERE id = ?");
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $edit_data = $result->fetch_assoc();
            $edit_name = $edit_data['name'];
        } else {
            echo "<p>No person found with the provided ID.</p>";
            exit;
        }
        $stmt->close();
    } else {
        echo "<p>Invalid ID provided for editing.</p>";
        exit;
    }
}

// Fetch all persons
$persons = $conn->query("SELECT * FROM persons");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Persons</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Manage Persons</h1>

    <!-- Navigation Links -->
    <nav>
        <ul>
            <li><a href="index.php" class="button">Back to Tool Management</a></li>
            <li><a href="manage_tools.php" class="button">Manage Tools</a></li>
            <li><a href="manage_types.php" class="button">Manage Types</a></li>
            <li><a href="manage_statuses.php" class="button">Manage Statuses</a></li>
            <li><a href="manage_locations.php" class="button">Manage Locations</a></li>
        </ul>
    </nav>

    <!-- Form to add or edit a person -->
    <form method="POST">
        <label for="name">Person Name:</label>
        <input type="text" id="name" name="<?= $edit_mode ? 'edit_name' : 'name' ?>" value="<?= htmlspecialchars($edit_name) ?>" required>
        <?php if ($edit_mode): ?>
            <input type="hidden" name="edit_id" value="<?= $edit_id ?>">
            <button type="submit" name="edit_person">Update Person</button>
            <a href="manage_persons.php">Cancel</a>
        <?php else: ?>
            <button type="submit" name="add_person">Add Person</button>
        <?php endif; ?>
    </form>

    <!-- Display all persons -->
    <h2>Existing Persons</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($persons && $persons->num_rows > 0): ?>
                <?php while ($person = $persons->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($person['id']) ?></td>
                        <td><?= htmlspecialchars($person['name']) ?></td>
                        <td>
                            <a href="manage_persons.php?edit=<?= $person['id'] ?>">Edit</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No persons found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
