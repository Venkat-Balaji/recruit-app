<?php
function load_bookings() {
    $file = 'bookings.csv';
    if (file_exists($file)) {
        return array_map('str_getcsv', file($file));
    }
    return [];
}

function get_time_slots($bookings, $selected_date) {
    $all_slots = [];
    for ($hour = 10; $hour < 17; $hour++) { // 10 AM to 4 PM
        $all_slots[] = "{$hour}:00 - " . ($hour + 1) . ":00";
    }

    // Get booked slots for the selected date
    $booked_slots = array_column(
        array_filter($bookings, function ($row) use ($selected_date) {
            return $row[2] === $selected_date && $row[0] === "HR";
        }), 
        3
    );

    // Return the available slots
    return array_diff($all_slots, $booked_slots);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_date = $_POST['selected_date'] ?? '';
    $bookings = load_bookings();
    $available_slots = get_time_slots($bookings, $selected_date);
    echo json_encode(array_values($available_slots));
}
?>
