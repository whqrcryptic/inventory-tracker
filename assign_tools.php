<?php
// assign_tools.php
include 'db.php';

// Fetch available tools and persons
$tools = $conn->query("SELECT * FROM tools WHERE person IS NULL");
$persons = $conn->query("SELECT * FROM persons");

// Handle assignment of a tool to a person
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_tool'])) {
    $tool_id = $_POST['tool_id'];
    $person_id = $_POST['person_id'];
    $checkout_date = date('Y-m-d');

    $stmt = $conn->prepare("UPDATE tools SET person = ?, checkout_date = ? WHERE id = ?");
    $stmt->bind_param("isi", $person_id, $checkout_date, $tool_id);
    if ($stmt->execute()) {
        echo "<p>Tool assigned successfully!</p>";
    } else {
        echo "<p>Error assigning tool: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Fetch checked-out tools
$checked_out_tools = $conn->query("SELECT t.*, p.name AS person_name FROM tools t LEFT JOIN persons p ON t.person = p.id WHERE t.person IS NOT NULL");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Tools</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Assign Tools</h1>

    <!-- Navigation Links -->
    <nav>
        <ul>
            <li><a href="index.php" class="button">Back to Tool Management</a></li>
            <li><a href="manage_tools.php" class="button">Manage Tools</a></li>
        </ul>
    </nav>

    <!-- Form to assign a tool -->
    <form method="POST">
        <label for="tool_id">Select Tool:</label>
        <select id="tool_id" name="tool_id" required>
            <option value="">Select a tool</option>
            <?php while ($tool = $tools->fetch_assoc()): ?>
                <option value="<?= $tool['id'] ?>"><?= htmlspecialchars($tool['item_name']) ?> (Barcode: <?= htmlspecialchars($tool['barcode']) ?>)</option>
            <?php endwhile; ?>
        </select>

        <label for="person_id">Assign to Person:</label>
        <select id="person_id" name="person_id" required>
            <option value="">Select a person</option>
            <?php while ($person = $persons->fetch_assoc()): ?>
                <option value="<?= $person['id'] ?>"><?= htmlspecialchars($person['name']) ?></option>
            <?php endwhile; ?>
        </select>

        <button type="submit" name="assign_tool" class="button">Assign Tool</button>
    </form>

    <!-- Display checked-out tools -->
    <h2>Checked-Out Tools</h2>
    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Barcode</th>
                <th>Assigned Person</th>
                <th>Checkout Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($checked_out_tools && $checked_out_tools->num_rows > 0): ?>
                <?php while ($tool = $checked_out_tools->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($tool['item_name']) ?></td>
                        <td><?= htmlspecialchars($tool['barcode']) ?></td>
                        <td><?= htmlspecialchars($tool['person_name']) ?></td>
                        <td><?= htmlspecialchars($tool['checkout_date']) ?></td>
                        <td>
                            <a href="assign_tools.php?checkin=<?= $tool['id'] ?>" class="button">Check In</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No tools currently checked out.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>