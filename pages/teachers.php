<?php
// Include database connection
require('../includes/db_connect.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $background_check_passed = isset($_POST['b_check']) ? 1 : 0;
    $annual_salary = $_POST['annual_salary'];

    // Prepare the SQL statement to insert data into the teachers table
    $query = "INSERT INTO teachers (name, address, phone_number, background_check_passed, annual_salary) 
              VALUES (:name, :address, :phone_number, :background_check_passed, :annual_salary)";
    $statement = $db->prepare($query);

    // Bind parameters
    $statement->bindParam(':name', $name);
    $statement->bindParam(':address', $address);
    $statement->bindParam(':phone_number', $phone_number);
    $statement->bindParam(':background_check_passed', $background_check_passed, PDO::PARAM_INT);
    $statement->bindParam(':annual_salary', $annual_salary);

    // Execute the statement
    if ($statement->execute()) {
        echo "Teacher added successfully.";
    } else {
        echo "Error: Unable to add teacher.";
    }
}

// SQL query to select all teachers
$query = 'SELECT * FROM teachers';
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
        <caption>Teachers</caption>
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Address</th>
                <th scope="col">Phone Number</th>
                <th scope="col">Annual Salary</th>
                <th scope="col">Background Check</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($db->query($query) as $class) : ?>
                <tr>
                    <td data-label="Name"><?= $class['name'] ?></td>
                    <td data-label="Address"><?= $class['address'] ?></td>
                    <td data-label="Phone Number"><?= $class['phone_number'] ?></td>
                    <td data-label="Amount Salary">$<?= $class['annual_salary'] ?></td>
                    <td data-label="Background Check"><?= $class['background_check_passed'] ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

    <h1>Add New Teacher</h1>

    <form action="" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" required>

        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" required>

        <label for="b_check">Background Check:</label>
        <input type="checkbox" id="b_check" name="b_check">

        <label for="annual_salary">Salary:</label>
        <input type="number" id="annual_salary" name="annual_salary" required>

        <button type="submit">Add Teacher</button>
    </form>
</body>

</html>
