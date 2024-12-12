<?php
// manage_tools.php
include 'db.php';

// Initialize variables for editing
$edit_mode = false;
$edit_id = '';
$tool_data = [
    'item_name' => '',
    'barcode' => '',
    'type_id' => '',
    'asset_value' => '',
    'stock_count' => '',
    'status_id' => '',
    'notes' => '',
    'bin' => '',
    'location' => ''
];

// Fetch data for dropdowns
$locations = $conn->query("SELECT * FROM locations");
$types = $conn->query("SELECT * FROM types");
$statuses = $conn->query("SELECT * FROM statuses");

// Handle adding a new tool
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_tool'])) {
    $tool_data = [
        'item_name' => $_POST['item_name'],
        'barcode' => $_POST['barcode'],
        'type_id' => $_POST['type_id'],
        'asset_value' => $_POST['asset_value'],
        'stock_count' => $_POST['stock_count'],
        'status_id' => $_POST['status_id'],
        'notes' => $_POST['notes'],
        'bin' => $_POST['bin'],
        'location' => $_POST['location_id']
    ];

    $stmt = $conn->prepare("INSERT INTO tools (item_name, barcode, type_id, asset_value, stock_count, status_id, notes, bin, location) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiiisss", $tool_data['item_name'], $tool_data['barcode'], $tool_data['type_id'], $tool_data['asset_value'], $tool_data['stock_count'], $tool_data['status_id'], $tool_data['notes'], $tool_data['bin'], $tool_data['location']);
    if ($stmt->execute()) {
        echo "<p>Tool added successfully!</p>";
    } else {
        echo "<p>Error adding tool: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Handle editing a tool
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_tool'])) {
    $edit_id = $_POST['edit_id'];
    $tool_data = [
        'item_name' => $_POST['item_name'],
        'barcode' => $_POST['barcode'],
        'type_id' => $_POST['type_id'],
        'asset_value' => $_POST['asset_value'],
        'stock_count' => $_POST['stock_count'],
        'status_id' => $_POST['status_id'],
        'notes' => $_POST['notes'],
        'bin' => $_POST['bin'],
        'location' => $_POST['location_id']
    ];

    $stmt = $conn->prepare("UPDATE tools SET item_name = ?, barcode = ?, type_id = ?, asset_value = ?, stock_count = ?, status_id = ?, notes = ?, bin = ?, location = ? WHERE id = ?");
    $stmt->bind_param("ssiiiisssi", $tool_data['item_name'], $tool_data['barcode'], $tool_data['type_id'], $tool_data['asset_value'], $tool_data['stock_count'], $tool_data['status_id'], $tool_data['notes'], $tool_data['bin'], $tool_data['location'], $edit_id);
    if ($stmt->execute()) {
        echo "<p>Tool updated successfully!</p>";
    } else {
        echo "<p>Error updating tool: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Handle loading data for editing
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_mode = true;

    $result = $conn->query("SELECT * FROM tools WHERE id = $edit_id");
    if ($result && $result->num_rows > 0) {
        $tool_data = $result->fetch_assoc();
    }
}

// Fetch all tools
$tools = $conn->query("SELECT t.*, l.location_name, ty.type_name, s.status_name 
                       FROM tools t 
                       LEFT JOIN locations l ON t.location = l.id
                       LEFT JOIN types ty ON t.type_id = ty.id
                       LEFT JOIN statuses s ON t.status_id = s.id
                       ORDER BY t.item_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tools</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Manage Tools</h1>

    <!-- Navigation Links -->
    <nav>
        <ul>
            <li><a href="index.php" class="button">Back to Tool Management</a></li>
            <li><a href="manage_persons.php" class="button">Manage Persons</a></li>
            <li><a href="manage_types.php" class="button">Manage Types</a></li>
            <li><a href="manage_statuses.php" class="button">Manage Statuses</a></li>
            <li><a href="manage_locations.php" class="button">Manage Locations</a></li>
            <li><a href="assign_tools.php" class="button">Assign Tools</a></li>
        </ul>
    </nav>

    <!-- Form to add or edit a tool -->
    <form method="POST">
        <label for="item_name">Item Name:</label>
        <input type="text" id="item_name" name="item_name" value="<?= htmlspecialchars($tool_data['item_name']) ?>" required>

        <label for="barcode">Barcode:</label>
        <input type="text" id="barcode" name="barcode" value="<?= htmlspecialchars($tool_data['barcode']) ?>" required>

        <label for="type_id">Type:</label>
        <select id="type_id" name="type_id" required>
            <option value="">Select a type</option>
            <?php while ($type = $types->fetch_assoc()): ?>
                <option value="<?= $type['id'] ?>" <?= $type['id'] == $tool_data['type_id'] ? 'selected' : '' ?>><?= htmlspecialchars($type['type_name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label for="asset_value">Asset Value:</label>
        <input type="number" id="asset_value" name="asset_value" value="<?= htmlspecialchars($tool_data['asset_value']) ?>" step="0.01" required>

        <label for="stock_count">Stock Count:</label>
        <input type="number" id="stock_count" name="stock_count" value="<?= htmlspecialchars($tool_data['stock_count']) ?>" required>

        <label for="status_id">Status:</label>
        <select id="status_id" name="status_id" required>
            <option value="">Select a status</option>
            <?php while ($status = $statuses->fetch_assoc()): ?>
                <option value="<?= $status['id'] ?>" <?= $status['id'] == $tool_data['status_id'] ? 'selected' : '' ?>><?= htmlspecialchars($status['status_name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label for="notes">Notes:</label>
        <textarea id="notes" name="notes"><?= htmlspecialchars($tool_data['notes']) ?></textarea>

        <label for="bin">Bin:</label>
        <input type="text" id="bin" name="bin" value="<?= htmlspecialchars($tool_data['bin']) ?>">

        <label for="location_id">Location:</label>
        <select id="location_id" name="location_id" required>
            <option value="">Select a location</option>
            <?php while ($location = $locations->fetch_assoc()): ?>
                <option value="<?= $location['id'] ?>" <?= $location['id'] == $tool_data['location'] ? 'selected' : '' ?>><?= htmlspecialchars($location['location_name']) ?></option>
            <?php endwhile; ?>
        </select>

        <?php if ($edit_mode): ?>
            <input type="hidden" name="edit_id" value="<?= $edit_id ?>">
            <button type="submit" name="edit_tool" class="button">Update Tool</button>
            <a href="manage_tools.php" class="button button-cancel">Cancel</a>
        <?php else: ?>
            <button type="submit" name="add_tool" class="button">Add Tool</button>
        <?php endif; ?>
    </form>

    <!-- Display all tools -->
    <h2>Existing Tools</h2>
    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Barcode</th>
                <th>Type</th>
                <th>Asset Value</th>
                <th>Stock Count</th>
                <th>Status</th>
                <th>Notes</th>
                <th>Bin</th>
                <th>Location</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($tools && $tools->num_rows > 0): ?>
                <?php while ($tool = $tools->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($tool['item_name']) ?></td>
                        <td><?= htmlspecialchars($tool['barcode']) ?></td>
                        <td><?= htmlspecialchars($tool['type_name']) ?></td>
                        <td><?= htmlspecialchars($tool['asset_value']) ?></td>
                        <td><?= htmlspecialchars($tool['stock_count']) ?></td>
                        <td><?= htmlspecialchars($tool['status_name']) ?></td>
                        <td><?= htmlspecialchars($tool['notes']) ?></td>
                        <td><?= htmlspecialchars($tool['bin']) ?></td>
                        <td><?= htmlspecialchars($tool['location_name']) ?></td>
                        <td>
                            <a href="manage_tools.php?edit=<?= $tool['id'] ?>" class="button">Edit</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10">No tools found or error fetching data.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
