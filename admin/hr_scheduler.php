<?php
// Load existing bookings from CSV if available
function load_bookings() {
    $file = 'bookings.csv';
    if (file_exists($file)) {
        return array_map('str_getcsv', file($file));
    }
    return [];
}

// Save bookings to CSV
function save_bookings($data) {
    $file = 'bookings.csv';
    $f = fopen($file, 'a');
    fputcsv($f, $data);
    fclose($f);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hr_name = $_POST['hr_name'];
    $selected_date = $_POST['selected_date'];
    $selected_slot = $_POST['selected_slot']; // Get selected time slot

    // Add new HR booking
    save_bookings(["HR", $hr_name, $selected_date, $selected_slot]);

    echo "<p>Date {$selected_date} at {$selected_slot} is booked for your interviews.</p>";
}

// Generate available dates (next 7 days)
$today = new DateTime();
$available_dates = [];
for ($i = 0; $i < 7; $i++) {
    $available_dates[] = $today->modify('+1 day')->format('Y-m-d');
}

// Exclude already booked HR dates
$bookings = load_bookings();
$hr_booked_dates = array_column(array_filter($bookings, function($row) {
    return $row[0] == "HR";
}), 2);
$available_dates = array_diff($available_dates, $hr_booked_dates);

// Generate available time slots for each day
function get_time_slots($bookings, $date) {
    $all_slots = [];
    for ($hour = 10; $hour < 17; $hour++) { // 10 AM to 4 PM
        $all_slots[] = "{$hour}:00 - " . ($hour + 1) . ":00";
    }

    // Get booked slots for the selected date
    $booked_slots = array_column(array_filter($bookings, function($row) use ($date) {
        return $row[2] == $date && $row[0] == "HR"; // Check for HR bookings
    }), 3);

    // Return available slots (those not booked yet)
    return array_diff($all_slots, $booked_slots);
}

// Initialize time slots
$time_slots = [];
if (isset($_POST['selected_date'])) {
    $time_slots = get_time_slots($bookings, $_POST['selected_date']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Interview Date Scheduler</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 10px;
            color: #555;
        }
        input[type="text"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        h2 {
            margin-top: 40px;
            color: #333;
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
            background-color: #f2f2f2;
        }
    </style>
    <script>
        function updateTimeSlots() {
            const selectedDate = document.getElementById('selected_date').value;
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'get_time_slots.php', true); // Create a separate file to handle AJAX
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const timeSlots = JSON.parse(xhr.responseText);
                    const slotSelect = document.getElementById('selected_slot');
                    slotSelect.innerHTML = ''; // Clear existing options
                    timeSlots.forEach(slot => {
                        const option = document.createElement('option');
                        option.value = slot;
                        option.textContent = slot;
                        slotSelect.appendChild(option);
                    });
                }
            };
            xhr.send('selected_date=' + encodeURIComponent(selectedDate));
        }
    </script>
</head>
<body>
    <h1>HR Interview Date Scheduler</h1>
    <form method="POST">
        <label for="hr_name">Enter your name:</label>
        <input type="text" name="hr_name" required>

        <label for="selected_date">Select a date:</label>
        <select name="selected_date" id="selected_date" required onchange="updateTimeSlots()">
            <?php foreach ($available_dates as $date): ?>
                <option value="<?php echo $date; ?>"><?php echo $date; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="selected_slot">Select a time slot:</label>
        <select name="selected_slot" id="selected_slot" required>
            <?php foreach ($time_slots as $slot): ?>
                <option value="<?php echo $slot; ?>"><?php echo $slot; ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Confirm Date</button>
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
