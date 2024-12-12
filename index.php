<?php
// index.php
include 'db.php';

// Fetch data for dropdowns
$persons = $conn->query("SELECT * FROM persons");
$locations = $conn->query("SELECT * FROM locations");
$types = $conn->query("SELECT * FROM types");
$statuses = $conn->query("SELECT * FROM statuses");

// Handle form submission for checking out a tool (existing code here)

// Handle check-in request (existing code here)

// Fetch all checked-out tools (existing code here)
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tool Room Checkout System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Tool Room Checkout System</h1>

        <!-- Navigation Links -->
        <nav>
            <ul>
            <li><a href="manage_persons.php" class="button">Manage Persons</a></li>
            <li><a href="manage_tools.php" class="button">Manage Tools</a></li>
            <li><a href="manage_types.php" class="button">Manage Types</a></li>
            <li><a href="manage_statuses.php" class="button">Manage Statuses</a></li>
            <li><a href="manage_locations.php" class="button">Manage Locations</a></li>
            </ul>
        </nav>

    </div>
</body>
</html>

        <!-- Checkout Form -->
        <form method="POST">
            <label for="item_name">Item Name:</label>
            <input type="text" id="item_name" name="item_name" placeholder="Item name" required>

            <label for="barcode">Scan Barcode:</label>
            <input type="text" id="barcode" name="barcode" placeholder="Scan barcode" required>

            <label for="person_id">Person Assigned:</label>
            <select id="person_id" name="person_id" required>
                <option value="">Select a person</option>
                <?php while ($person = $persons->fetch_assoc()): ?>
                    <option value="<?= $person['id'] ?>"><?= htmlspecialchars($person['name'] ?? '') ?></option>
                <?php endwhile; ?>
            </select>

            <label for="type_id">Type:</label>
            <select id="type_id" name="type_id" required>
                <option value="">Select a type</option>
                <?php while ($type = $types->fetch_assoc()): ?>
                    <option value="<?= $type['id'] ?>" <?= $type['id'] == $tool_data['type_id'] ? 'selected' : '' ?>><?= htmlspecialchars($type['type_name']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="asset_value">Asset Value:</label>
            <input type="number" id="asset_value" name="asset_value" placeholder="Asset value" step="0.01" required>

            <label for="stock_count">Stock Count:</label>
            <input type="number" id="stock_count" name="stock_count" placeholder="Stock count" required>

            <label for="status_id">Status:</label>
            <select id="status_id" name="status_id" required>
                <option value="">Select a status</option>
                <?php while ($status = $statuses->fetch_assoc()): ?>
                    <option value="<?= $status['id'] ?>"><?= htmlspecialchars($status['status_name'] ?? '') ?></option>
                <?php endwhile; ?>
            </select>

            <label for="notes">Notes:</label>
            <textarea id="notes" name="notes" placeholder="Additional notes"></textarea>

            <label for="bin">Bin:</label>
            <input type="text" id="bin" name="bin" placeholder="Bin location">

            <label for="location_id">Location:</label>
            <select id="location_id" name="location_id" required>
                <option value="">Select a location</option>
                <?php while ($location = $locations->fetch_assoc()): ?>
                    <option value="<?= $location['id'] ?>"><?= htmlspecialchars($location['location_name'] ?? '') ?></option>
                <?php endwhile; ?>
            </select>

            <label for="checkout_date">Checkout Date:</label>
            <input type="date" id="checkout_date" name="checkout_date" required>

            <button type="submit" name="checkout">Check Out</button>
        </form>

        <!-- Display Checked Out Tools -->
        <h2>Checked Out Tools</h2>
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Barcode</th>
                    <th>Person</th>
                    <th>Type</th>
                    <th>Asset Value</th>
                    <th>Stock Count</th>
                    <th>Status</th>
                    <th>Notes</th>
                    <th>Bin</th>
                    <th>Checkout Date</th>
                    <th>Check-in Date</th>
                    <th>Location</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($tools && $tools->num_rows > 0): ?>
                    <?php while ($tool = $tools->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($tool['item_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($tool['barcode'] ?? '') ?></td>
                            <td><?= htmlspecialchars($tool['person_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($tool['type_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($tool['asset_value'] ?? '') ?></td>
                            <td><?= htmlspecialchars($tool['stock_count'] ?? '') ?></td>
                            <td><?= htmlspecialchars($tool['status_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($tool['notes'] ?? '') ?></td>
                            <td><?= htmlspecialchars($tool['bin'] ?? '') ?></td>
                            <td><?= htmlspecialchars($tool['checkout_date'] ?? '') ?></td>
                            <td><?= htmlspecialchars($tool['checkin_date'] ?? 'Not checked in') ?></td>
                            <td><?= htmlspecialchars($tool['location_name'] ?? '') ?></td>
                            <td>
                                <?php if (!$tool['checkin_date']): ?>
                                    <a href="?checkin=<?= $tool['id'] ?>">Check In</a>
                                <?php else: ?>
                                    Checked In
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="13">No tools checked out or error fetching data.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
