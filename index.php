<!DOCTYPE HTML>  
<html lang="en">
    <head>
        <?php
        // Require Reports Generator
        require 'reports_generator_api.php';
        ?>
        <title><?php echo $CLIENT_NAME ?>-Dashboard</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    </head>
    <body>  
        <?php
        // Form Processing
        // define variables and set to empty values
        $start_date = $end_date = "";
        $end_dateErr = $start_dateErr = "";
        $date_parsed_start = $date_parsed_end = "";
        $form_err = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Check if given start date
            if (empty($_POST["start_date"])) {
                $start_dateErr = "Start date is required";
            } else {
                $start_date = $_POST["start_date"];
                $date_parsed_start = date_parse($start_date);
                // Check if date complies to checks
                $start_date = real_date($date_parsed_start);
                if (! $start_date){
                    $start_dateErr = "Poorly formatted end date";
                }
            }
            // Check if given end date
            if (empty($_POST["end_date"])) {
                $end_dateErr = "End date is required";
            } else {
                $end_date = $_POST["end_date"];
                $date_parsed_end = date_parse($end_date);
                // Check if date complies to checks
                $end_date = real_date($date_parsed_end);
                if (! $end_date){
                    $end_dateErr = "Poorly formatted end date";
                }
            }
            // Print form error if start date or end date is off 
            if (! ($start_date && $end_date)){
                $form_err .= "Poorly formatted start or end date";
            }
        }

        function real_date( $date_arr )
        {
            if ($date_arr["day"] && $date_arr["month"] && $date_arr["year"]) {
                $date_str = $date_arr["year"] . "-" . $date_arr["month"] . "-" . $date_arr["day"];
                return $date_str;
            } else {
                return false;
            }
        }
        ?>
        <div class="container" style="margin-top:100px;">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <div class="form-row" >
                    <div class="col-md-3 form-group">
                        <input type="text" class="form-control" name="start_date" placeholder="Start Date: YYYY-MM-DD">
                        <span class="text-danger"><?php echo $start_dateErr;?></span>
                    </div>
                    <div class="col-md-3 form-group">
                        <input type="text" class="form-control" name="end_date" placeholder="End Date: YYYY-MM-DD">
                        <span class="text-danger"><?php echo $end_dateErr;?></span>
                    </div>
                    <div class="col-md-1 form-group">
                        <input class="form-control" type="submit" name="submit" value="Submit">
                    </div>
                </div>
            </form>
            <div class="row" >
                <div class="col-md-12"><span class="text-danger"><?php echo $form_err;?></span></div>
            </div>
            <?php
            // Generate A Report
            if ($start_date && $end_date){
                $report_obj = new Report($start_date, $end_date, $ANALYTICS, $VIEW_ID);
                $report = $report_obj->generateReport();
                $report_obj->getQuickReportData();
            }
            ?>
            
            <div class="row">
                <div class="col-md-12">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Bounce Rate</th>
                                <th scope="col">Page Views</th>
                                <th scope="col">Adsense Revenue</th>
                                <th scope="col">Adsense Ad Units Viewed</th>
                                <th scope="col">Average Adsense Revenue Per Page View</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th><?php echo $start_date . "<br>" . $end_date ?></th>
                                <td><?php
                                    if($report_obj->quick_report){
                                        printf("%4.5f",$report_obj->quick_report["bounce"]);
                                    }
                                    ?>
                                </td>
                                <td><?php echo $report_obj->quick_report["sessions"] ?></td>
                                <td><?php echo $report_obj->quick_report["revenue"] ?></td>
                                <td><?php echo $report_obj->quick_report["ads_viewed"] ?></td>
                                <td>
                                    <?php
                                    if($report_obj->quick_report){
                                        $sessions = $report_obj->quick_report["sessions"];
                                        $revenue = $report_obj->quick_report["revenue"];
                                        printf("$ %4.8f", $revenue / $sessions);
                                    }
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
    </body>
</html>
