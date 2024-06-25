<?php
// Include database connection
require('../includes/db_connect.php');

// Query to get available pupils for dropdown
$query_pupils = "SELECT p.pupil_id, p.name
                FROM pupils AS p
                LEFT JOIN PupilGuardians AS pg ON p.pupil_id = pg.pupil_id
                GROUP BY p.pupil_id
                HAVING COUNT(pg.guardian_id) < 2;";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $db->beginTransaction();  // Start transaction for data consistency

        // Retrieve form data
        $name = $_POST["name"];
        $address = $_POST["address"];
        $email = $_POST["email"];
        $phone_number = $_POST["phone_number"];
        $pupil_id = $_POST["pupil_id"]; // Assuming pupil ID is selected from a dropdown or provided

        // 1. Insert Guardian Data
        $stmt_guardian = $db->prepare("INSERT INTO guardians (name, address, email, phone_number) VALUES (:name, :address, :email, :phone_number)");
        $stmt_guardian->bindParam(':name', $name);
        $stmt_guardian->bindParam(':address', $address);
        $stmt_guardian->bindParam(':email', $email);
        $stmt_guardian->bindParam(':phone_number', $phone_number);

        if (!$stmt_guardian->execute()) {
            throw new PDOException("Error adding guardian.");
        }

        // 2. Get Newly Inserted Guardian ID
        $guardian_id = $db->lastInsertId();

        // 3. Create Relationship (Pupil-Guardian)
        $stmt_relationship = $db->prepare("INSERT INTO PupilGuardians (pupil_id, guardian_id) VALUES (:pupil_id, :guardian_id)");
        $stmt_relationship->bindParam(':pupil_id', $pupil_id);
        $stmt_relationship->bindParam(':guardian_id', $guardian_id);

        if (!$stmt_relationship->execute()) {
            throw new PDOException("Error creating relationship.");
        }

        $db->commit();  // Commit the transaction if all insertions successful
        echo "Guardian created successfully!";
    } catch (PDOException $e) {
        $db->rollBack(); // Rollback if any exception occurs
        echo "Error adding guardian and creating relationship: " . $e->getMessage();
    }
}

// Query to select guardians and associated pupil
$query = 'SELECT g.guardian_id AS guardian_id, g.name AS name, g.address AS address, g.email AS email,g.phone_number AS phone_number, p.name AS pupil_name FROM guardians AS g INNER JOIN PupilGuardians AS pg ON g.guardian_id = pg.guardian_id INNER JOIN pupils AS p ON pg.pupil_id = p.pupil_id;';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="../css/style.css">
    <title>Guardians</title>
</head>

<body>
    <table>
        <caption>Guardians</caption>
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Address</th>
                <th scope="col">Email</th>
                <th scope="col">Phone Number</th>
                <th scope="col">Pupil</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($db->query($query) as $guardian) : ?>
                <tr>
                    <td data-label="Name"><?= $guardian['name'] ?></td>
                    <td data-label="Address"><?= $guardian['address'] ?></td>
                    <td data-label="Email"><?= $guardian['email'] ?></td>
                    <td data-label="Phone Number"><?= $guardian['phone_number'] ?></td>
                    <td data-label="Pupil"><?= $guardian['pupil_name'] ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

    <h1>Add Guardian & Assign to Pupil</h1>

    <form action="" method="post">
        <h2>Guardian Information</h2>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="address">Address:</label>
        <textarea id="address" name="address" required></textarea>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="phone_number">Phone Number:</label>
        <input type="tel" id="phone_number" name="phone_number" required>

        <h2>Assign to Pupil</h2>
        <label for="pupil_id">Select Pupil:</label>
        <select name="pupil_id" id="pupil_id" style="width: 98%; padding: 10px; border: 1px solid #ccc;margin-bottom: 15px;" required>
            <option value="">Select Pupil</option>
            <?php foreach ($db->query($query_pupils) as $pupil) : ?>
                <option value="<?= $pupil['pupil_id'] ?>"><?= $pupil['name'] ?></option>
            <?php endforeach ?>
        </select>

        <button type="submit">Add Guardian & Assign</button>
    </form>
</body>

</html>
