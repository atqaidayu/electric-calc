<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$voltage = $current = $current_rate = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $voltage = $_POST['voltage'];
    $current = $_POST['current'];
    $current_rate = $_POST['current_rate'];

    // Validate input
    if (!is_numeric($voltage)) {
        $error = "Please enter valid numbers for voltage";
    } else if (!is_numeric($current)) {
        $error = "Please enter valid numbers for current";
    } else if (!is_numeric($current_rate)) {
        $error = "Please enter valid numbers for current rate";
    } else { 
        // Calculate power in kilowatt/hour
        $power = $voltage * $current/1000;

        // Convert current_rate into rate (RM)
        $rate = $current_rate/100;

        // Initialize results array
        $results = [];

        // Calculate energy and total cost for each hour
        for ($hour = 1; $hour <= 24; $hour++) {
            $energy = $power * $hour;
            $total = $energy * $rate;

            $results[] = [
                'hour' => $hour,
                'energy' => number_format($energy, 5), // Rounded to 5 decimal places
                'total' => number_format($total, 2) // Rounded to 2 decimal places
            ];
        }

        // Calculate total per day
        $total_per_day = array_sum(array_column($results, 'total'));
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Electricity Calculator </title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Electricity Calculator</h2>

    <!-- Form for user input -->
    <form method="POST" action="">

        <div class="form-group">
            <label for="voltage">Voltage (V)</label>
            <input type="text" class="form-control" id="voltage" name="voltage" value="<?php echo isset($voltage) ? $voltage : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="current">Current (A)</label>
            <input type="text" class="form-control" id="current" name="current" value="<?php echo isset($current) ? $current : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="current_rate">Current Rate (sen/kWh)</label>
            <input type="text" class="form-control" id="current_rate" name="current_rate" value="<?php echo isset($current_rate) ? $current_rate : ''; ?>" required>
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
            
            <div class= "my-4">
                    Power(kW) :  <?php echo number_format($power, 5); ?>
            </div>

            <div class= "my-4" role="alert">
                    Rate : RM<?php echo number_format($rate, 3); ?>
            </div>

            <div class= "my-4" role="alert">
                    Total Per Day: RM<?php echo number_format($total_per_day, 2);  ?>
            </div>
           
        </div>

        <div class="mt-4"> 
            <h3>Total Per Hours</h3>
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
