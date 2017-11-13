<?php

// Load the tokens file
$str = '{"access_token":"ya29.Glv9BIFBH_vXmWojR30fztTZ7ywEGhuKn3uWGJwibWA54KGCuaSgDmkxDCxc8C8CPWgZKlQTdiqLZQd6jm1Y0pl_UwMYDFISDME-dLfU7lc2R9vsyx17yPHajC0C","token_type":"Bearer","refresh_token":"1\/GNfiNMLFUHXz9u0-cpYn1srDk3mJG1VJuzhbE4lpPc8qe7FGKOuSXo3fNx-y34LI","expires_in":3600,"created":​1510095884 }';
// Decode the tokens file
$json_tokens = json_decode($str);
var_dump($json_tokens); 
//echo '<pre>' . $json_tokens[] . '</pre>';

// Load the Google API PHP Client Library.
require_once __DIR__ . '/vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setApplicationName('HalfHourMeals');
$client->setClientId('451466387551-s2rqui0i3ui9f2cf0lj310go5s9m1sp7.apps.googleusercontent.com');
$client->setClientSecret('_IVq4O_ZkB48xwWcCQaJvsqO');
$client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');
$client->setAccessType('offline');
$client->setDeveloperKey('451466387551-s2rqui0i3ui9f2cf0lj310go5s9m1sp7.apps.googleusercontent.com');

$client->addScope(Google_Service_Analytics::ANALYTICS_READONLY);

$access_token_from_db = json_decode('XXXXXX');
$refresh_token_from_db = 'XXXXX';
$_tokenArray['access_token'] = $access_token_from_db;

//must be set as json
$client->setAccessToken( json_encode($_tokenArray) );

//check if token expired:
if ( $client->isAccessTokenExpired() ){
    $client->refreshToken($refresh_token_from_db);
    $new_access_token = $client->getAccessToken();
}
  
$client->setAccessToken($_SESSION['access_token']);


// if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
//   echo("nothing to worry about here");
//   // Set the access token on the client.

//   // Create an authorized analytics service object.
//   $analytics = new Google_Service_AnalyticsReporting($client);

//   // Call the Analytics Reporting API V4.
//   $response = getReport($analytics);

//   // Print the response.
//   printResults($response);

// } else {
//     echo("y u no work");
// }


// /**
//  * Queries the Analytics Reporting API V4.
//  *
//  * @param service An authorized Analytics Reporting API V4 service object.
//  * @return The Analytics Reporting API V4 response.
//  */
// function getReport($analytics) {

//   // Replace with your view ID, for example XXXX.
//   $VIEW_ID = "HalfHourMeals";

//   // Create the DateRange object.
//   $dateRange = new Google_Service_AnalyticsReporting_DateRange();
//   $dateRange->setStartDate("7daysAgo");
//   $dateRange->setEndDate("today");

//   // Create the Metrics object.
//   $sessions = new Google_Service_AnalyticsReporting_Metric();
//   $sessions->setExpression("ga:sessions");
//   $sessions->setAlias("sessions");

//   // Create the ReportRequest object.
//   $request = new Google_Service_AnalyticsReporting_ReportRequest();
//   $request->setViewId($VIEW_ID);
//   $request->setDateRanges($dateRange);
//   $request->setMetrics(array($sessions));

//   $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
//   $body->setReportRequests( array( $request) );
//   return $analytics->reports->batchGet( $body );
// }


// /**
//  * Parses and prints the Analytics Reporting API V4 response.
//  *
//  * @param An Analytics Reporting API V4 response.
//  */
// function printResults($reports) {
//   for ( $reportIndex = 0; $reportIndex < count( $reports ); $reportIndex++ ) {
//     $report = $reports[ $reportIndex ];
//     $header = $report->getColumnHeader();
//     $dimensionHeaders = $header->getDimensions();
//     $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
//     $rows = $report->getData()->getRows();

//     for ( $rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
//       $row = $rows[ $rowIndex ];
//       $dimensions = $row->getDimensions();
//       $metrics = $row->getMetrics();
//       for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
//         print($dimensionHeaders[$i] . ": " . $dimensions[$i] . "\n");
//       }

//       for ($j = 0; $j < count($metrics); $j++) {
//         $values = $metrics[$j]->getValues();
//         for ($k = 0; $k < count($values); $k++) {
//           $entry = $metricHeaders[$k];
//           print($entry->getName() . ": " . $values[$k] . "\n");
//         }
//       }
//     }
//   }
// }
