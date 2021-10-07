<?php

if(!empty($_REQUEST)){
    if(function_exists($_REQUEST['action'])){
        call_user_func($_REQUEST['action']);
    }
    die();
}

function setDays(){
    
    $button_id = $_REQUEST['btn_id'];
    $qnt_days = $_REQUEST['days'];
    
    $id = preg_replace("/[^0-9]/", '', $button_id);
    
    $mysqli = new mysqli('localhost','f0520793_INC','Didi1683759','f0520793_INC');
    if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
    }
    
    $results = $mysqli->query("UPDATE f0520793_INC.EVENTS E
    SET E.DAYS_REMAIN = E.DAYS_REMAIN + '$qnt_days'
    WHERE E.ID = '$id'");
    $mysqli->commit();
    $mysqli->close();
    
}


function setCheck(){
    $check_box = $_REQUEST['chek_id'];
    
    $id = preg_replace("/[^0-9]/", '', $check_box);
    
    $mysqli = new mysqli('localhost','f0520793_INC','Didi1683759','f0520793_INC');
    if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
    }
    
    $results = $mysqli->query("UPDATE f0520793_INC.EVENTS E
    SET E.FLAGS = 1
    WHERE E.ID = '$id'");
    $mysqli->commit();
    $mysqli->close();
    
}

function getEventsData(){
    $mysqli = new mysqli('localhost','f0520793_INC','Didi1683759','f0520793_INC');
    if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
    }
    
    $data = $mysqli->query("SELECT E.DATE_CHAR, E.DAYS_REMAIN, E.COMM, STR_TO_DATE(E.DATE_CHAR, '%d.%m.%Y') as PRIV_DATE, 
DATE_ADD(STR_TO_DATE(E.DATE_CHAR, '%d.%m.%Y'), INTERVAL E.DAYS_REMAIN DAY) as REMA, 
DATEDIFF(DATE_ADD(STR_TO_DATE(E.DATE_CHAR, '%d.%m.%Y'), INTERVAL E.DAYS_REMAIN DAY),SYSDATE()) as HOW_MANY_DAYS_YET,
E.ID, E.FLAGS
FROM f0520793_INC.EVENTS E
ORDER BY DATEDIFF(SYSDATE(),DATE_ADD(STR_TO_DATE(E.DATE_CHAR, '%d.%m.%Y'), INTERVAL E.DAYS_REMAIN DAY)) DESC");
    
    $i = 0;
    $dataArray = array();
    while ($row = $data->fetch_assoc()) {
    
    /*$dataArray[$i]['DAYS_REMAIN'] = $row['DAYS_REMAIN'];
    $dataArray[$i]['COMM'] = $row['COMM'];
    $UNIX_END = strtotime($row['DATE_CHAR']) + $row['DAYS_REMAIN']*24*60*60; //дата конца события
    $UNIX_END_YYYY_DD = date('d.m.Y',$UNIX_END);
    $UNIX_AFTER = $UNIX_END - time();
    $UNIX_AFTER_DAYS = ceil($UNIX_AFTER/(24*60*60));
    $dataArray[$i]['UNIX_END_YYYY_DD'] = $UNIX_END_YYYY_DD;
    $dataArray[$i]['UNIX_AFT_DAYS'] = $UNIX_AFTER_DAYS;*/
    //$dataArray[$i]['DATE_CHAR'] = $row['DATE_CHAR'];
    //$dataArray[$i]['DAYS_REMAIN'] = $row['DAYS_REMAIN'];
    

    $UNIX_END = strtotime($row['DATE_CHAR']) + $row['DAYS_REMAIN']*24*60*60; //дата конца события
    $UNIX_END_YYYY_DD = date('d.m.Y',$UNIX_END);
    $UNIX_AFTER = $UNIX_END - time();
    $UNIX_AFTER_DAYS = ceil($UNIX_AFTER/(24*60*60));
    $dataArray[$i]['0'] = $row['DATE_CHAR'];
    $dataArray[$i]['1'] = $UNIX_END_YYYY_DD;
    $dataArray[$i]['2'] = $UNIX_AFTER_DAYS;
    $dataArray[$i]['3'] = $row['COMM'];
    $dataArray[$i]['4'] = $row['FLAGS'];
    $dataArray[$i]['5'] = $row['ID'];
    $i++;
    }
    
    echo json_encode($dataArray);
}

function createEvent(){
    $date = $_REQUEST['date'];
    $comm = $_REQUEST['comm'];
    $diff = $_REQUEST['diff'];
    
    $mysqli = new mysqli('localhost','f0520793_INC','Didi1683759','f0520793_INC');
    if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
    }
    
    //$curDate = date('H:i:s d.m.Y');
    
    $results = $mysqli->query("INSERT INTO f0520793_INC.EVENTS(DATETIM, DATE_CHAR, COMM, DAYS_REMAIN) VALUES (sysdate(),'$date', '$comm', $diff)");
    $mysqli->commit();
    $mysqli->close(); 
    
    echo json_encode($date);
}

function getData(){
    
    $type = $_REQUEST['type'];
    $host = 'localhost'; // адрес сервера 
    $database = 'f0520793_INC'; // имя базы данных
    $user = 'f0520793_INC'; // имя пользователя
    $password = 'Didi1683759'; // пароль

    $mysqli = new mysqli('localhost','f0520793_INC','Didi1683759','f0520793_INC');
    if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
    }
    
    if($type == 'temp'){
        $parametr = "TEMPERATURE";
    }
    else if($type == 'press'){
        $parametr = "PRESSURE";
    }
    else if($type == 'humi'){
        $parametr = "HUMIDITY";
    }
    
    $data = $mysqli->query("SELECT ".$parametr." as VALUE, DATE_LINUX 
    FROM HUMIDATA 
    WHERE DATE_LINUX BETWEEN ((SELECT MAX(DATE_LINUX) FROM HUMIDATA) - 86400) AND (SELECT MAX(DATE_LINUX) FROM HUMIDATA)
    /*WHERE DATE_LINUX BETWEEN 1618220842 and 1618221024*/
    ");
    
    $i = 0;
    $dataArray = array();
    while ($row = $data->fetch_assoc()) {
    $dataArray[$i]['DATE'] = $row['DATE_LINUX'];
    $dataArray[$i]['VAL'] = $row['VALUE'];
    $i++;
    }
    
    echo json_encode($dataArray);
}

?>