var typeReq;

$( "#temp_div" ).click(function() {
    typeReq = "temp";
    showChart(typeReq);
});
$( "#hum_div" ).click(function() {
    typeReq = "humi";
    showChart(typeReq);
});
$( "#press_div" ).click(function() {
    typeReq = "press";
    showChart(typeReq);
});
$( "#calendar_div" ).click(function() {
    
    var element=document.getElementById('cssworldru8');
    
    document.getElementById('chartdiv').innerHTML = "";

    //отрисовка таблицы с данными по растюхам
    $.ajax({
    type: 'POST',
    url: '/core_graph/functions.php',
    dataType: 'json',
    data: {
       action: "getEventsData"
    },
    success: function(data){
        printTable('chartdiv', data);
    }
    });
    
    if(!element)
    {
        xCal("cal_form", {
        id: "cssworldru8", // Задать уникальный ID
        "class": "xcalend", // CSS класс оформления внешнего вида
        hide: 0, // Не скрывать календарь
        lang: 'ru',
        x: 0, // Отключить кнопку закрытия календаря
        autoOff:0,
        to: "cal_form", // Разместить календарь внутри элемента с id=date8
        fn: calendClick
        });
    } 
    else 
    {
        document.getElementById('cssworldru8').remove();
    }
});



function printTable(parentID, data){
    
    var header = ["Дата начала","Дата окончания", "Дней до", "Событие", "Управление"];
    
    var _br = document.createElement('br');
    
    var _tbl = document.createElement('table');
    _tbl.setAttribute('border', '2');
    _tbl.setAttribute('cellpadding', '4');
    _tbl.id = 'tbl_events';
    
    for(var i = 0; i < data.length + 1; i++){
        
        var _tr_head = document.createElement('tr');
        
            for(var j = 0; j < header.length; j++){
                
                if(i == 0){
                    var _tb_head = document.createElement('td');
                    _tb_head.innerHTML = header[j];
                    _tr_head.appendChild(_tb_head);
                }
                else{
                    
                    var _tb_head = document.createElement('td');
                    
                    if(j < header.length - 1){
                        
                        _tb_head.innerHTML = data[i-1][j];
                        
                    }
                    else{
                        
                        var _check_box = document.createElement('input');
                        _check_box.type = 'checkbox';
                        _check_box.id = 'check_' + data[i-1][5];
                        
                        var _button = document.createElement('button');
                        _button.id = 'button_' + data[i-1][5];
                        _button.innerHTML = '+';
                        
                        if( (data[i-1][2] > 2) || (data[i-1][4] != 0) ){
                            _check_box.disabled = true;
                            _button.disabled = true;
                        }
                        
                        if( (data[i-1][4] != 0) ){
                            _check_box.checked = true;
                        }
                        
                        _tb_head.appendChild(_check_box);
                        _tb_head.appendChild(_button);
                    }
                    if(data[i-1][4] != 0){
                        _tr_head.setAttribute('style', 'color: grey');
                    }
                    _tr_head.appendChild(_tb_head);
                    
                }
            }
        
        _tbl.appendChild(_tr_head);
    }
document.getElementById(parentID).appendChild(_br);    
document.getElementById(parentID).appendChild(_tbl);
}


$(document).on('click', 'input[id^="check_"]', function(data){
    
    var cur = document.getElementById(this.id).id;
    
    var isDone = confirm("Подтверждаете закрытие таска?");
    
    if(isDone){
        $.ajax({
            type: 'POST',
            url: '/core_graph/functions.php',
            dataType: 'json',
            data: {
               action: "setCheck",
               chek_id: cur
            },
            success: function(data){
                document.getElementById(this.id).checked = true;
                document.getElementById(this.id).disabled = true;
            }
            });
    }
    else{
        document.getElementById(this.id).checked = false;
    }
});


$(document).on('click', 'button[id^="button_"]', function(data){
    
    var cur = document.getElementById(this.id).id;
    
    var howDays = prompt("Сколько дней добавить?", "0");
    
    if(howDays > 0){
        $.ajax({
            type: 'POST',
            url: '/core_graph/functions.php',
            dataType: 'json',
            data: {
               action: "setDays",
               btn_id: cur,
               days: howDays
            },
            success: function(data){
            }
            });
    }

});


function calendClick(){
    var comment = prompt('Зарегистрировать событие?', 'Кол-во дней "/" событие');
    var dateOf = arguments[0];
    var arrayOfData = comment.split('/');

    
    if(comment !== null && arrayOfData[0] > 0){
        $.ajax({
            type: 'POST',
            url: '/core_graph/functions.php',
            dataType: 'json',
            data: {
               action: "createEvent",
               date: dateOf,
               diff: arrayOfData[0],
               comm: arrayOfData[1]
            },
            success: function(dataArr){
                //console.log(dataArr + '__111');
           }
        });
    }
}

//var typeReq = "temp";

function showChart(parameter){
$.ajax({
   type: 'POST',
   url: '/core_graph/functions.php',
   dataType: 'json',
   data: {
       action: "getData",
       type: parameter
   },
   success: function(dataArray){

       var data = [];
       for (var i = 0; i < dataArray.length; i++) {
           
           let inputDate = (dataArray[i].DATE)*1000;
           let inpetVal = dataArray[i].VAL;

           data.push({ date: inputDate, value: inpetVal });
           
       }
       
       am4core.useTheme(am4themes_animated);

        var chart = am4core.create("chartdiv", am4charts.XYChart);
        chart.paddingRight = 20;

        chart.data = data;
        
        var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
        dateAxis.renderer.grid.template.location = 0;
        
        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.tooltip.disabled = true;
        valueAxis.renderer.minWidth = 35;
        
        var series = chart.series.push(new am4charts.LineSeries());
        series.dataFields.dateX = "date";
        series.dataFields.valueY = "value";
        series.tooltipText = "{valueY}";
        series.tooltip.pointerOrientation = "vertical";
        series.tooltip.background.fillOpacity = 0.5;
        
        chart.cursor = new am4charts.XYCursor();
        chart.cursor.snapToSeries = series;
        chart.cursor.xAxis = dateAxis;
        
        var scrollbarX = new am4charts.XYChartScrollbar();
        scrollbarX.series.push(series);
        chart.scrollbarX = scrollbarX;
       

   }
});
}

/*
function printTable(parentID, data){
    
    var header = ["Дата начала","Дата окончания", "Дней до", "Событие", "Флаг"];
    
    var _br = document.createElement('br');
    
    var _tbl = document.createElement('table');
    _tbl.setAttribute('border', '2');
    _tbl.setAttribute('cellpadding', '4');
    _tbl.id = 'tbl_events';
    
    for(var i = 0; i < data.length + 1; i++){
        
        var _tr_head = document.createElement('tr');
        
            for(var j = 0; j < header.length; j++){
                
                if(i == 0){
                    var _tb_head = document.createElement('td');
                    _tb_head.innerHTML = header[j];
                    _tr_head.appendChild(_tb_head);
                }
                else{
                    
                    var _tb_head = document.createElement('td');
                    
                    if(j < header.length - 1){
                        
                        _tb_head.innerHTML = data[i-1][j];
                        
                    }
                    else{
                        
                        var _check_box = document.createElement('input');
                        _check_box.type = 'checkbox';
                        _check_box.id = 'check_' + data[i-1][5];
                        
                        
                        if( (data[i-1][2] > 2) || (data[i-1][4] != 0) ){
                            _check_box.disabled = true;
                        }
                        
                        if( (data[i-1][4] != 0) ){
                            _check_box.checked = true;
                        }
                        
                        _tb_head.appendChild(_check_box);
                    }
                    if(data[i-1][4] != 0){
                        _tr_head.setAttribute('style', 'color: grey');
                    }
                    _tr_head.appendChild(_tb_head);
                    
                }
            }
        
        _tbl.appendChild(_tr_head);
    }
document.getElementById(parentID).appendChild(_br);    
document.getElementById(parentID).appendChild(_tbl);
}
*/