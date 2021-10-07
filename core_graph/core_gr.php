<?php

require_once './connection.php';

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

?>