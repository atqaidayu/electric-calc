<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables
$voltage = $current = $current_rate = '';
$error = '';
$results = [];
$total_per_day = 0;

// Function to validate numeric input
function validate_input($input) {
    return is_numeric($input);
}

// Function to calculate power in kW
function calculate_power($voltage, $current) {
    return $voltage * $current / 1000;
}

// Function to convert rate from sen/kWh to RM/kWh
function convert_rate($current_rate) {
    return $current_rate / 100;
}

// Function to calculate energy and total cost for each hour
function calculate_energy_and_cost($power, $rate) {
    $results = [];
    for ($hour = 1; $hour <= 24; $hour++) {
        $energy = $power * $hour;
        $total = $energy * $rate;
        $results[] = [
            'hour' => $hour,
            'energy' => number_format($energy, 5),
            'total' => number_format($total, 2)
        ];
    }
    return $results;
}

// Form handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $voltage = $_POST['voltage'];
    $current = $_POST['current'];
    $current_rate = $_POST['current_rate'];

    // Validate input
    if (!validate_input($voltage)) {
        $error = "Please enter valid numbers for voltage";
    } else if (!validate_input($current)) {
        $error = "Please enter valid numbers for current";
    } else if (!validate_input($current_rate)) {
        $error = "Please enter valid numbers for current rate";
    } else { 
        // Calculate power in kW
        $power = calculate_power($voltage, $current);

        // Convert current_rate into rate (RM)
        $rate = convert_rate($current_rate);

        // Calculate energy and total cost for each hour
        $results = calculate_energy_and_cost($power, $rate);

        // Calculate total cost per day
        $total_per_day = array_sum(array_column($results, 'total'));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electricity Calculator</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Electricity Calculator</h2>

    <!-- Form for user input -->
    <form method="POST" action="">
        <div class="form-group">
            <label for="voltage">Voltage (V)</label>
            <input type="text" class="form-control" id="voltage" name="voltage" value="<?php echo htmlspecialchars($voltage); ?>" required>
        </div>
        <div class="form-group">
            <label for="current">Current (A)</label>
            <input type="text" class="form-control" id="current" name="current" value="<?php echo htmlspecialchars($current); ?>" required>
        </div>
        <div class="form-group">
            <label for="current_rate">Current Rate (sen/kWh)</label>
            <input type="text" class="form-control" id="current_rate" name="current_rate" value="<?php echo htmlspecialchars($current_rate); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Calculate</button>
    </form>

    <!-- Display error message for input validation -->
    <?php if (!empty($error)) { ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php } ?>

    <!-- Display calculation results if available -->
    <?php if (!empty($results)) { ?>
        <div class="container border border-primary rounded p-3">
            <div class="my-4">Power (kW): <?php echo number_format($power, 5); ?></div>
            <div class="my-4">Rate: RM<?php echo number_format($rate, 3); ?></div>
            <div class="my-4">Total Per Day: RM<?php echo number_format($total_per_day, 2); ?></div>
        </div>

        <div class="mt-4">
            <h3>Total Per Hour</h3>
        </div>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Hour</th>
                <th>Energy (kWh)</th>
                <th>Total (RM)</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($results as $result) { ?>
                <tr>
                    <td><?php echo $result['hour']; ?></td>
                    <td><?php echo $result['energy']; ?></td>
                    <td><?php echo $result['total']; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>
</body>
</html>
