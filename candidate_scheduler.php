<?php
// Load existing bookings from CSV if available
function load_bookings() {
    $file = 'C:\xampp\htdocs\Recruitment_Management_System\admin\bookings.csv';
    if (file_exists($file)) {
        return array_map('str_getcsv', file($file));
    }
    return [];
}

// Save bookings to CSV
function save_bookings($data) {
    $file = 'C:\xampp\htdocs\Recruitment_Management_System\admin\bookings.csv';
    $f = fopen($file, 'a');
    fputcsv($f, $data);
    fclose($f);
}

// Function to get available slots for a given date
function get_available_slots($date, $bookings) {
    $all_slots = [];
    for ($hour = 10; $hour < 17; $hour++) { // 10 AM to 4 PM (17)
        $all_slots[] = "{$hour}:00 - " . ($hour + 1) . ":00";
    }

    // Get all booked slots for the selected date by HR
    $hr_booked_slots = array_column(array_filter($bookings, function($row) use ($date) {
        return $row[2] == $date && $row[0] == "HR"; // Filter only HR bookings
    }), 3);

    // Return available slots (those booked by HR)
    return array_values(array_intersect($all_slots, $hr_booked_slots));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $candidate_name = $_POST['candidate_name'];
    $selected_date = $_POST['selected_date'];
    $selected_slot = $_POST['selected_slot'];

    // Save candidate's booking
    save_bookings(["Candidate", $candidate_name, $selected_date, $selected_slot]);

    echo "<p>Your interview is scheduled on {$selected_date} at {$selected_slot}.</p>";
}

// Load existing bookings
$bookings = load_bookings();

// Generate available dates (next 7 days)
$today = new DateTime();
$available_dates = [];
for ($i = 0; $i < 7; $i++) {
    $available_dates[] = $today->modify('+1 day')->format('Y-m-d');
}

// Exclude dates that do not have HR booked slots
$hr_booked_dates = array_unique(array_column(array_filter($bookings, function($row) {
    return $row[0] == "HR"; // Filter only HR bookings
}), 2));

// Available dates for candidates that have HR bookings
$available_dates = array_intersect($available_dates, $hr_booked_dates);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Interview Scheduler</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        h1, h2 {
            color: #2c3e50;
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #2980b9;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #3498db;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #2980b9;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
    <script>
        function updateTimeSlots() {
            const selectedDate = document.getElementById('selected_date').value;
            const bookings = <?php echo json_encode($bookings); ?>;
            const availableSlots = getAvailableSlots(selectedDate, bookings);
            const slotSelect = document.getElementById('selected_slot');

            // Clear previous options
            slotSelect.innerHTML = '';

            // Populate available slots
            availableSlots.forEach(slot => {
                const option = document.createElement('option');
                option.value = slot;
                option.textContent = slot;
                slotSelect.appendChild(option);
            });
        }

        function getAvailableSlots(date, bookings) {
            const allSlots = [];
            for (let hour = 10; hour < 17; hour++) {
                allSlots.push(`${hour}:00 - ${hour + 1}:00`);
            }

            // Get booked slots for the selected date by HR
            const hrBookedSlots = bookings.filter(row => row[2] === date && row[0] === "HR")
                                          .map(row => row[3]);

            return allSlots.filter(slot => hrBookedSlots.includes(slot));
        }
    </script>
</head>
<body>
    <h1>Candidate Interview Scheduler</h1>
    <form method="POST">
        <label for="candidate_name">Enter your name:</label>
        <input type="text" name="candidate_name" required><br><br>

        <label for="selected_date">Select a date for your interview:</label>
        <select name="selected_date" id="selected_date" required onchange="updateTimeSlots()">
            <option value="">Select a date</option>
            <?php foreach ($available_dates as $date): ?>
                <option value="<?php echo $date; ?>"><?php echo $date; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="selected_slot">Select an available time slot:</label>
        <select name="selected_slot" id="selected_slot" required>
            <option value="">Select a slot</option>
            <!-- Options will be populated dynamically -->
        </select><br><br>

        <button type="submit">Confirm Slot</button>
    </form>

    <!-- <h2>Bookings (For Testing):</h2> -->
    <!-- <table>
        <tr>
            <th>Role</th>
            <th>Name</th>
            <th>Date</th>
            <th>Slot</th>
        </tr>
        <?php foreach ($bookings as $booking): ?>
            <tr>
                <td><?php echo $booking[0]; ?></td>
                <td><?php echo $booking[1]; ?></td>
                <td><?php echo $booking[2]; ?></td>
                <td><?php echo $booking[3]; ?></td>
            </tr>
        <?php endforeach; ?>
    </table> -->
</body>
</html>
