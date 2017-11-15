<?php

$DEBUG = false;

// Load the tokens file
$realToken = '{"access_token":"ya29.Glv9BIFBH_vXmWojR30fztTZ7ywEGhuKn3uWGJwibWA54KGCuaSgDmkxDCxc8C8CPWgZKlQTdiqLZQd6jm1Y0pl_UwMYDFISDME-dLfU7lc2R9vsyx17yPHajC0C","token_type":"Bearer","refresh_token":"1\/GNfiNMLFUHXz9u0-cpYn1srDk3mJG1VJuzhbE4lpPc8qe7FGKOuSXo3fNx-y34LI","expires_in":3600,"created":1510095884}';

if ($DEBUG) {
    echo "My Token which is valid total string length: " . mb_strlen($realToken) . "<br />";
    echo "<pre>";var_dump(json_decode($realToken));
}

// Decode the tokens file
$json_tokens = json_decode($realToken);

if ($DEBUG) {
    var_dump($json_tokens);
}

require_once __DIR__ . '/vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setApplicationName('HalfHourMeals');
$client->setClientId('451466387551-s2rqui0i3ui9f2cf0lj310go5s9m1sp7.apps.googleusercontent.com');
$client->setClientSecret('_IVq4O_ZkB48xwWcCQaJvsqO');
$client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');
$client->setAccessType('offline');
//thats a client id, not a dev key, you dont need to set it for the dev key - alan
$client->addScope(Google_Service_Analytics::ANALYTICS_READONLY);

// you also dont need to clear the old token. You can leave the old token and just check if its expired.

$client->setAccessToken( $realToken /*json_encode($_tokenArray)*/ );
//check if token expired:
if ( $client->isAccessTokenExpired() ){	
    $client->refreshToken($json_tokens->refresh_token);
    $new_access_token = $client->getAccessToken();

    if ($DEBUG) {
        echo "<pre>";
        print_r($new_access_token);
    }
}

// Create an authorized analytics service object.
//$analytics = new Google_Service_AnalyticsReporting($client);

// Call the Analytics Reporting API V4.
//$response = getReport($analytics);
// var_dump($response);

//printResults($response);


/**
 * Queries the Analytics Reporting API V4.
 *
 * @param service An authorized Analytics Reporting API V4 service object.
 * @return The Analytics Reporting API V4 response.
 */
function getReport($analytics, $start_date, $end_date) {

  // Replace with your view ID, for example XXXX.
  $VIEW_ID = "80618290";

  // Create the DateRange object.
  $dateRange = new Google_Service_AnalyticsReporting_DateRange();
  $dateRange->setStartDate("7daysAgo");
  $dateRange->setEndDate("today");

  // Create Metrics object for sessions
  $sessions = new Google_Service_AnalyticsReporting_Metric();
  $sessions->setExpression("ga:sessions");
  $sessions->setAlias("sessions");

  // Create Metrics object for bounce rate
  $bounce = new Google_Service_AnalyticsReporting_Metric();
  $bounce->setExpression("ga:bounceRate");
  $bounce->setAlias("bounce");

  // Create Metrics Object for pageviews
  $views = new Google_Service_AnalyticsReporting_Metric();
  $views->setExpression("ga:pageviews");
  $views->setAlias("views");

  // Create Metrics Object for adsense revenue
  $revenue = new Google_Service_AnalyticsReporting_Metric();
  $revenue->setExpression("ga:adsenseRevenue");
  $revenue->setAlias("revenue");

  // Create Metrics Object for 
  $ads_viewed = new Google_Service_AnalyticsReporting_Metric();
  $ads_viewed->setExpression("ga:adsenseAdUnitsViewed");
  $ads_viewed->setAlias("ads_viewed");
  
  // Create the ReportRequest object.
  $request = new Google_Service_AnalyticsReporting_ReportRequest();
  $request->setViewId($VIEW_ID);
  $request->setDateRanges($dateRange);
  $request->setMetrics(array($sessions, $bounce, $views, $revenue, $ads_viewed));

  $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
  $body->setReportRequests( array( $request) );
  return $analytics->reports->batchGet( $body );
}


/**
 * Parses and prints the Analytics Reporting API V4 response.
 *
 * @param An Analytics Reporting API V4 response.
 */
function printResults($reports) {
  for ( $reportIndex = 0; $reportIndex < count( $reports ); $reportIndex++ ) {
    $report = $reports[ $reportIndex ];
    $header = $report->getColumnHeader();
    $dimensionHeaders = $header->getDimensions();
    $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
    $rows = $report->getData()->getRows();

    for ( $rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
      $row = $rows[ $rowIndex ];
      $dimensions = $row->getDimensions();
      $metrics = $row->getMetrics();
      for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
        print($dimensionHeaders[$i] . ": " . $dimensions[$i] . "\n");
      }

      for ($j = 0; $j < count($metrics); $j++) {
        $values = $metrics[$j]->getValues();
        for ($k = 0; $k < count($values); $k++) {
          $entry = $metricHeaders[$k];
          print($entry->getName() . ": " . $values[$k] . "\n");
        }
      }
    }
  }
}


$client->setAccessToken($new_access_token);