<?PHP
require_once 'connection.php';
//    /?func=set&all_inc=23

$function = $_GET['func'];
$qnt_all = $_GET['all_inc'];
$qnt_my = $_GET['my_inc'];
$my_time = $_GET['my_time'];
$curr_time = $_GET['cur_date'];
$qnt_ra = $_GET['all_ra'];
$qnt_my_ra = $_GET['my_ra'];

$filename = __DIR__.'file.txt';

define('LOWER_LIMIT_HUM', '31');

if($function == 'set'){
    
    /*file_put_contents($filename, '');
    $fd = fopen($filename, 'a');
    $data_string = "^".$qnt_all."|".$qnt_my."|".$my_time."|".$curr_time;
    if($fd){
    fwrite($fd, $data_string);
    fclose($fd);
    }*/
    

    $mysqli = new mysqli('localhost','f0520793_INC','Didi1683759','f0520793_INC');
    if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
    }
    
    
    /* Мои умножаю на 5. Это добавит веса! */
    $pre_work = $mysqli->query("SELECT IFNULL( (ALL_QNT + MY_QNT*5 + ALL_RA + MY_RA*5), 0) as ALL_UNITS FROM INC WHERE ASRZ_UNIX_TIME = (SELECT MAX(ASRZ_UNIX_TIME) FROM INC)");
    $row = $pre_work->fetch_object();
    $sum_units_DB_now = $row->ALL_UNITS;   
    
    $sum_units_to_write = $qnt_all + $qnt_my + $qnt_ra + $qnt_my_ra;
    
    if( ($sum_units_DB_now == 0) || ($sum_units_to_write != 0) ){
        //если на текущий момент в БД 0 ИЛИ пришел НЕ ноль пишем всё, что бы не пришло...
    $results = $mysqli->query("INSERT INTO f0520793_INC.INC(ALL_QNT, MY_QNT, ALL_RA, MY_RA, TIME_DEAD, ASRZ_UNIX_TIME) VALUES ($qnt_all, $qnt_my,$qnt_ra,$qnt_my_ra,  $my_time, $curr_time)");
    $mysqli->commit();
    $mysqli->close(); 
    }
    elseif( ($sum_units_DB_now > 4) && ($sum_units_to_write == 0)  ){
        /* Оставлю как заглушку - скорее всего, пришли нули  */
    }
    elseif( ($sum_units_DB_now > 0) && ($sum_units_DB_now <= 4) && ($sum_units_to_write == 0) ){
        //ну, поверим в команду! :)
    $results = $mysqli->query("INSERT INTO f0520793_INC.INC(ALL_QNT, MY_QNT, ALL_RA, MY_RA, TIME_DEAD, ASRZ_UNIX_TIME) VALUES ($qnt_all, $qnt_my,$qnt_ra,$qnt_my_ra,  $my_time, $curr_time)");
    $mysqli->commit();
    $mysqli->close();         
    }


    
}
else if($function == 'get'){
    
    
    /*$str=file_get_contents($filename);
    $str = substr($str, 1);
    $arr = explode('|', $str);
    $arr[3] = round( (time() - $arr[3])/(60*60), 2 ); //часов назад была синхра
    $str = '^'.implode("|", $arr);
	print_r($str);*/
	
	$mysqli = new mysqli('localhost','f0520793_INC','Didi1683759','f0520793_INC');
    if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
    }

    $results = $mysqli->query("SELECT ALL_QNT, MY_QNT, ALL_RA, MY_RA, TIME_DEAD, ASRZ_UNIX_TIME FROM INC WHERE ASRZ_UNIX_TIME = (SELECT MAX(ASRZ_UNIX_TIME) FROM INC)");
    $row = $results->fetch_object();
    
    $qnt_all = $row->ALL_QNT;
    $qnt_me = $row->MY_QNT;
    $qnt_ra = $row->ALL_RA;
    $qnt_my_ra = $row->MY_RA;
    $time_end = $row->TIME_DEAD;
    $time = $row->ASRZ_UNIX_TIME;
    $send_time = round( (time() - $time)/(60*60), 2 );
    
    echo '^'.$qnt_all.'|'.$qnt_me.'|'.$qnt_ra.'|'.$qnt_my_ra.'|'.$time_end.'|'.$send_time;
    $mysqli->close();
}
else if($function == 'test'){
/*    $link = mysqli_connect($host, $user, $password, $database);
    $query = 'SELECT MY_QNT FROM INC';
    $result = mysqli_real_query($link, $query);
    mysqli_close($link);
    print_r($result);*/
    
}
else if($function == 'setHmData'){
    
    //http://f0520793.xsph.ru/?func=setHmData&sens=DHT11&hum=18&temp=20
    
    $sensor = $_GET['sens'];
    $humidity = $_GET['hum'];
    $temperature = $_GET['temp'];
    $pressure = $_GET['press']; 
    $altitude = $_GET['alt']; 
    $currTimeUNIX = time();
    $currTimeYY = date('H:i:s d.m.Y');

    $mysqli = new mysqli('localhost','f0520793_INC','Didi1683759','f0520793_INC');
    if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
    }
    
    if( ($temperature > 0) && ($humidity > 0) ){
    $results = $mysqli->query("INSERT INTO f0520793_INC.HUMIDATA (SENSOR, TEMPERATURE, HUMIDITY, DATE_LINUX, DATE_YYYY, PRESSURE) 
    VALUES ('$sensor', $temperature, $humidity, $currTimeUNIX, '$currTimeYY', $pressure)");
    $mysqli->commit();
    $mysqli->close(); 
    }
    
    if( ($humidity < LOWER_LIMIT_HUM) ){
       
       #отправка боту в телегу инфы!
       
       $textToChat = "Влажность+меньше+лимита+(лимит+-+".LOWER_LIMIT_HUM."%)+.+Сейчас+влажность:+".$humidity."%";
       
       $result = file_get_contents('https://api.telegram.org/bot1871844490:AAEH6NiODP5gbhaF9eeY4RYx6C1awZu8sTY/sendMessage?chat_id=-578368221&text='.$textToChat);
       
    }
}

?>
