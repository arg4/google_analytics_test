<?php
// Load Google Libraries w/ composer
require_once __DIR__ . '/vendor/autoload.php';

// Load the tokens file
$auth_token = '{"access_token":"ya29.Glv9BIFBH_vXmWojR30fztTZ7ywEGhuKn3uWGJwibWA54KGCuaSgDmkxDCxc8C8CPWgZKlQTdiqLZQd6jm1Y0pl_UwMYDFISDME-dLfU7lc2R9vsyx17yPHajC0C","token_type":"Bearer","refresh_token":"1\/GNfiNMLFUHXz9u0-cpYn1srDk3mJG1VJuzhbE4lpPc8qe7FGKOuSXo3fNx-y34LI","expires_in":3600,"created":1510095884}';

// Decode the tokens file
$json_tokens = json_decode($auth_token);

// Start Session
session_start();

// Setup
$client = new Google_Client();
$client->setApplicationName('HalfHourMeals');
$client->setClientId('451466387551-s2rqui0i3ui9f2cf0lj310go5s9m1sp7.apps.googleusercontent.com');
$client->setClientSecret('_IVq4O_ZkB48xwWcCQaJvsqO');
$client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');
$client->setAccessType('offline');
$client->addScope(Google_Service_Analytics::ANALYTICS_READONLY);

$client->setAccessToken( $auth_token /*json_encode($_tokenArray)*/ );
//check if token expired:
if ( $client->isAccessTokenExpired() ){	
    $client->refreshToken($json_tokens->refresh_token);
    $new_access_token = $client->getAccessToken();
    $client->setAccessToken($new_access_token);
}

// Create an authorized analytics service object.
$ANALYTICS = new Google_Service_AnalyticsReporting($client);
$CLIENT_NAME = 'Half Hour Meals';
$VIEW_ID = "80618290";


/**
 * Queries the Analytics Reporting API V4.
 *
 * @param service An authorized Analytics Reporting API V4 service object.
 * @return The Analytics Reporting API V4 response.
 */
class Report {
    var $start_date;
    var $end_date;
    var $analytics;
    var $view_id;

    var $full_report;
    var $quick_report = array();

    // Constructs a Report Object
    function __construct($start, $end, $ana, $id){
        $this->start_date = $start;
        $this->end_date = $end;
        $this->analytics = $ana;
        $this->view_id = $id;
    }

    // Generates A Report
    function generateReport(){
        // Create the DateRange object.
        $dateRange = new Google_Service_AnalyticsReporting_DateRange();
        $dateRange->setStartDate($this->start_date);
        $dateRange->setEndDate($this->end_date);

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
        $request->setViewId($this->view_id);
        $request->setDateRanges($dateRange);
        $request->setMetrics(array($sessions, $bounce, $views, $revenue, $ads_viewed));

        $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests( array( $request) );
        $this->full_report =  $this->analytics->reports->batchGet( $body );
        return $this->full_report;
    }
    
    // Returns the full body of the report if it exists
    function getFullReport(){
        if ($this->full_report){
            return $this->full_report;
        } else {
            return false;
        }
    }

    // Generates a condensed version of the report
    function getQuickReportData(){
        $reports = $this->full_report;
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
                
                for ($j = 0; $j < count($metrics); $j++) {
                    $values = $metrics[$j]->getValues();
                    for ($k = 0; $k < count($values); $k++) {
                        $entry = $metricHeaders[$k];
                        $this->quick_report[$entry->getName()] = $values[$k];
                    }
                }
            }
        }
    }
}
