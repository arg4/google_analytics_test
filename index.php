
<!DOCTYPE HTML>  
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>  

<?php
// define variables and set to empty values
$start_date = $end_date = "";
$end_dateErr = $start_dateErr = "";
$date_parsed_start = $date_parsed_end = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["start_date"])) {
    $start_dateErr = "Start date is required";
  } else {
    $start_date = $_POST["start_date"];
    $date_parsed_start = date_parse($start_date);
    echo real_date($date_parsed_start);
  }
  if (empty($_POST["end_date"])) {
    $end_dateErr = "End date is required";
  } else {
    $end_date = $_POST["end_date"];
    $date_parsed_end = date_parse($end_date);
    echo real_date($date_parsed_end);
  }
}

function real_date( $date_arr )
{
    if ($date_arr["day"] && $date_arr["month"] && $date_arr["year"]) {
        $date_str = $date_arr["year"] . "-" . $date_arr["month"] . "-" $date_arr["day"];
        return $date_str;
    } else {
        return false;
    }
}
?>

<h2>PHP Form Validation Example</h2>
<p><span class="error">* required field.</span></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
Start Date: <input type="text" name="start_date">
  <span class="error">* <?php echo $start_dateErr;?></span>
  <br><br>
End Date: <input type="text" name="end_date">
  <span class="error">* <?php echo $end_dateErr;?></span>
  <input type="submit" name="submit" value="Submit">  
</form>

<?php

// Call reports generator
require 'reports_generator_api.php';

// Return Report
echo "<h2>Your Input:</h2>";
echo "<br>";
echo $start_date . "<br>" . $date_parsed_start["month"] . "<br>";
var_dump($date_parsed_start);
echo "<br>";
echo $end_date . "<br>" . $date_parsed_end["month"] . "<br>";
var_dump($date_parsed_end);
?>

</body>
</html>