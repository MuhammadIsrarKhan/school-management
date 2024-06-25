<?php
// Include database connection file
require('../includes/db_connect.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST["name"];
    $capacity = $_POST["capacity"];
    $teacher_id = $_POST["teacher_id"]; // Assuming the teacher_id is submitted from the form

    // Prepare and execute the SQL query to insert the data
    $stmt = $db->prepare("INSERT INTO classes (class_name, capacity, teacher_id) VALUES (:name, :capacity, :teacher_id)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':capacity', $capacity);
    $stmt->bindParam(':teacher_id', $teacher_id);

    // Execute the query
    if ($stmt->execute()) {
        // Insertion successful
        echo "Class added successfully.";
    } else {
        // Insertion failed
        echo "Error adding class.";
    }
}

// Query to retrieve existing classes along with assigned teacher names
$query = "SELECT classes.class_id, classes.class_name, classes.capacity, IFNULL(teachers.name, 'No teacher assigned') AS name 
          FROM classes 
          LEFT JOIN teachers ON classes.teacher_id = teachers.teacher_id;";

// Query to retrieve available teachers (teachers not assigned to any class)
$query2 = "SELECT teachers.teacher_id, teachers.name
           FROM teachers
           WHERE teachers.teacher_id NOT IN (
               SELECT teacher_id
               FROM classes
               WHERE teacher_id IS NOT NULL
           );";
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
    <!-- Display existing classes -->
    <table>
        <caption>Classes</caption>
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Capacity</th>
                <th scope="col">Teacher Name</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($db->query($query) as $class) : ?>
                <tr>
                    <td data-label="Name"><?= $class['class_name'] ?></td>
                    <td data-label="Capacity"><?= $class['capacity'] ?></td>
                    <td data-label="Teacher Name"><?= $class['name'] ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

    <!-- Form to add a new class -->
    <h1>Add New Class</h1>
    <form action="" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="capacity">Capacity:</label>
        <input type="number" id="capacity" name="capacity" required>

        <label for="teacher">Assign Teacher:</label>
        <div class="select-wrapper" style="margin-bottom: 20px;">
            <select name="teacher_id" id="teacher_id" style="width: 98%; padding: 10px; border: 1px solid #ccc;margin-bottom: 15px;">
                <option value="">Select one</option>
                <?php foreach ($db->query($query2) as $teacher) : ?>
                    <option value="<?= $teacher['teacher_id'] ?>"><?= $teacher['name'] ?></option>
                <?php endforeach ?>
            </select>
        </div>

        <button type="submit">Add Class</button>
    </form>
</body>

</html>
