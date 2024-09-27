// Step 1: The user selects a stylist from the dropdown list, which is dynamically populated from the stylists table.
// Step 2: Once the user selects a stylist and submits the form, available time slots (not booked) for the selected
stylist
// are fetched from the time_slots table and displayed.
// Step 3: The user provides their details, selects a time slot, and books the appointment. The selected time slot is
// marked as booked.

-- Stylists Table
CREATE TABLE stylists (
stylist_id INT PRIMARY KEY AUTO_INCREMENT,
name VARCHAR(255) NOT NULL
);

-- Time Slots Table
CREATE TABLE time_slots (
slot_id INT PRIMARY KEY AUTO_INCREMENT,
stylist_id INT,
slot_time TIME NOT NULL,
slot_date DATE NOT NULL,
is_booked BOOLEAN DEFAULT 0,
FOREIGN KEY (stylist_id) REFERENCES stylists(stylist_id)
);

-- Bookings Table
CREATE TABLE bookings (
booking_id INT PRIMARY KEY AUTO_INCREMENT,
stylist_id INT,
slot_id INT,
user_name VARCHAR(255) NOT NULL,
user_email VARCHAR(255) NOT NULL,
FOREIGN KEY (stylist_id) REFERENCES stylists(stylist_id),
FOREIGN KEY (slot_id) REFERENCES time_slots(slot_id)
);

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
</head>

<body>
    <h2>Select a Stylist</h2>
    <form action="book_appointment.php" method="POST">
        <label for="stylist">Choose a stylist:</label>
        <select name="stylist_id" id="stylist">
            <?php
            // Fetch stylists from the database
            $conn = new mysqli('localhost', 'root', '', 'appointments_db');
            $query = "SELECT * FROM stylists";
            $result = $conn->query($query);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['stylist_id']}'>{$row['name']}</option>";
            }
            ?>
        </select>
        <input type="submit" name="select_stylist" value="Next">
    </form>
</body>

</html>

<?php
$conn = new mysqli('localhost', 'root', '', 'appointments_db');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['select_stylist'])) {
        $stylist_id = $_POST['stylist_id'];

        // Step 2: Fetch available time slots for the selected stylist
        $query = "SELECT * FROM time_slots WHERE stylist_id = $stylist_id AND is_booked = 0";
        $result = $conn->query($query);

        echo "<h2>Select an available time slot</h2>";
        echo "<form action='book_appointment.php' method='POST'>";
        echo "<input type='hidden' name='stylist_id' value='$stylist_id'>";

        echo "<label for='slot'>Choose a time slot:</label>";
        echo "<select name='slot_id' id='slot'>";
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row['slot_id']}'>{$row['slot_date']} - {$row['slot_time']}</option>";
        }
        echo "</select>";
        echo "<br>";

        echo "<label for='name'>Your Name:</label>";
        echo "<input type='text' name='user_name' required>";
        echo "<br>";

        echo "<label for='email'>Your Email:</label>";
        echo "<input type='email' name='user_email' required>";
        echo "<br>";

        echo "<input type='submit' name='book_appointment' value='Book'>";
        echo "</form>";
    }

    // Step 3: Book the appointment
    if (isset($_POST['book_appointment'])) {
        $stylist_id = $_POST['stylist_id'];
        $slot_id = $_POST['slot_id'];
        $user_name = $_POST['user_name'];
        $user_email = $_POST['user_email'];

        // Insert booking
        $query = "INSERT INTO bookings (stylist_id, slot_id, user_name, user_email)
                  VALUES ('$stylist_id', '$slot_id', '$user_name', '$user_email')";
        $conn->query($query);

        // Mark the slot as booked
        $query = "UPDATE time_slots SET is_booked = 1 WHERE slot_id = $slot_id";
        $conn->query($query);

        echo "<h3>Appointment booked successfully!</h3>";
    }
}

$conn->close();
?>