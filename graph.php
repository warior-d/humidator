<html>
	<head>
	    <?php require_once "core_graph/core_gr.php"; ?>
		<meta charset="UTF-8">
		<title>Температура и влажность</title>
		<link rel="stylesheet" href="scripts/amcharts/index.css" />
		<link rel="stylesheet" href="scripts/css/stili.css" />
		
		
		<style>
			#chartdiv {
			  margin: 0 auto;
			  width: 800px;
			  height: 500px;
			}
		</style>

			<!-- Resources scripts/amcharts/-->
        <script src="scripts/jQuery.js"></script>
	</head>
	<body>
	    <div id = "lastSyncDate">Последняя синхронизация: <?php echo $date;?> (<?php echo $date_diff;?> секунд назад)</div>
	    <br>
	    <table border = "0" align="center">
	        <tr>
	            <td align = "center"> 
	            <div id="temp_div" class="modern">Температура <br><?php echo $temp;?></div>
	            </td>     
	            <td align = "center">
	            <div id="hum_div" class="modern">Влажность <br><?php echo $humi;?></div>
	            </td>
	            <td align = "center">
	            <div id="press_div" class="modern">Давление <br><?php echo $pres;?></div>
	            </td>
	            <td align = "center">
	            <div id="calendar_div" class="modern">Календарь<br><?php echo "(кликни)";?></div>
	            <div id="cal_form"></div>
	            </td>
	        </tr>         
	        <tr>
	            <td colspan="4" align = "center">
	                <div id="chartdiv"></div>			
	                <script src="scripts/amcharts/core.js"></script>
			        <script src="scripts/amcharts/charts.js"></script>
			        <script src="scripts/amcharts/animated.js"></script>
			        <script src="scripts/amcharts/onclicks.js"></script>
			        <script src="scripts/amcharts/main_graph.js"></script>
			        		<!-- календарь -->
		<link rel="stylesheet" type="text/css" href="scripts/calendar/cssworld.ru-xcal.css" />
        <script type="text/javascript" src="scripts/calendar/cssworld.ru-xcal-en.js"></script>
	            </td>
	        </tr>
	    </table>
	    
		
	</body>
	
</html>