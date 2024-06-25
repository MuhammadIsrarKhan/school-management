<?php
// Include database connection
require('../includes/db_connect.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST["name"];
    $address = $_POST["address"];
    $medical_information = $_POST["medical_information"];
    $class_id = $_POST["class_id"];

    // Prepare and execute the SQL query to insert the data
    $stmt = $db->prepare("INSERT INTO pupils (name, address, medical_info, class_id) VALUES (:name, :address, :medical_info, :class_id)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':medical_info', $medical_information);
    $stmt->bindParam(':class_id', $class_id);

    // Execute the query
    if ($stmt->execute()) {
        // Insertion successful
        echo "Pupil added successfully.";
    } else {
        // Insertion failed
        echo "Error adding pupil.";
    }
}

// SQL query to select all pupils with their associated class names
$query = 'SELECT pupil_id, name, address, medical_info, class_name FROM pupils JOIN classes ON pupils.class_id = classes.class_id;';

// Get available classes with capacity remaining
$query_classes = "SELECT class_id, class_name, capacity, (capacity - (SELECT COUNT(*) FROM pupils WHERE class_id = classes.class_id)) AS available_slots
                   FROM classes
                   HAVING available_slots > 0;";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="../css/style.css">
    <title>Teachers</title>
</head>

<body>
    <table>
        <caption>Pupils</caption>
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Address</th>
                <th scope="col">Medical Information</th>
                <th scope="col">Class Name</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($db->query($query) as $class) : ?>
                <tr>
                    <td data-label="Name"><?= $class['name'] ?></td>
                    <td data-label="Address"><?= $class['address'] ?></td>
                    <td data-label="Medical Information"><?= $class['medical_info'] ?></td>
                    <td data-label="Class Name"><?= $class['class_name'] ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

    <h1>Add New Pupil</h1>

    <form action="" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="address">Address:</label>
        <textarea id="address" name="address" required></textarea>

        <label for="medical_information">Medical Information:</label>
        <textarea id="medical_information" name="medical_information" required></textarea>

        <label for="class_id">Assign Class:</label>
        <select name="class_id" id="class_id" style="width: 98%; padding: 10px; border: 1px solid #ccc; margin-bottom: 15px;" required>
            <option value="">Select Class</option>
            <?php foreach ($db->query($query_classes) as $class) : ?>
                <option value="<?= $class['class_id'] ?>"><?= $class['class_name'] ?></option>
            <?php endforeach ?>
        </select>

        <button type="submit">Add Pupil</button>
    </form>
</body>

</html>
