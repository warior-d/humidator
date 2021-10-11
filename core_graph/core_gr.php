<?php

/* require_once './connection.php';

    $mysqli = new mysqli('localhost','f0520793_INC','Didi1683759','f0520793_INC');
    if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
    }
    
    $data = $mysqli->query("SELECT SENSOR, TEMPERATURE, HUMIDITY, PRESSURE, DATE_LINUX 
    FROM HUMIDATA 
    WHERE DATE_LINUX = (SELECT MAX(DATE_LINUX) FROM HUMIDATA)");
    $row = $data->fetch_object();
    $temp = $row->TEMPERATURE; 
    $pres = $row->PRESSURE; 
    $humi = $row->HUMIDITY; 
    $date =  date('d.m.Y H:i:s', $row->DATE_LINUX);
    $date_diff = time() - $row->DATE_LINUX;
    
function getData(){
    echo json_encode("111");
}
 */
 
require_once '../connection.php';

$dbconn = pg_connect("host=".$host." dbname=".$database." user=".$user." password=".$password);

$query_db = 'SELECT SENSOR, TEMPERATURE, HUMIDITY, PRESSURE, DATE_LINUX 
    FROM wifi_humidyty.humidata 
    WHERE DATE_LINUX = (SELECT MAX(DATE_LINUX) FROM wifi_humidyty.humidata)';
$query_result = pg_query($query_db);
 
$result = pg_fetch_all($query_result);

$temp = $result['0']['temperature'];
$pres = $result['0']['pressure'];
$humi = $result['0']['humidity'];


$date =  date('d.m.Y H:i:s', $result['0']['date_linux']);
$date_diff = time() - $result['0']['date_linux'];
$date_diff = $date_diff."hhh";

?>