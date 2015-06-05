window.onload = function () {
	getData();
}

$(document).ready(function(){
    $("#formulario").submit(function(e){
        e.stopPropagation();
        e.preventDefault();
        getData();
        return false;
    })
});

function getData(){
    $("#estado").removeClass('oculto');
    $("#estado").html('Cargando...');
	var postData = $("#formulario").serializeArray();
	var formURL = './getData.php';//$("#formularioIdentificarse").attr("action");
	$.ajax(
	{
	    url : './getData.php',
	    type: "POST",
	    data : postData,
	    success:function(data, textStatus, jqXHR)
	    {
            var dataParsed = JSON.parse(cleanString(data));
            if (dataParsed == null){
                $("#estado").html('Sin datos para mostrar en las fechas seleccionadas');
            }else{
                $("#estado").addClass('oculto');
    	        renderData(dataParsed);
                calcularAreaBajoLaCurva(dataParsed.canal1);
            }
	    }
	});
}

function renderData(datos){
    var chart = new CanvasJS.Chart("chartContainer", {
      title:{
        text: "Grafico..."
    },
    axisX:{
        title: "Linea del Tiempo",
        gridThickness: 1,
        valueFormatString: "HH:mm"
    },
    axisY: {
        title: "Temperatura"
    },
    axisY2: {
        title: "Radiacion"
    },    
    data: [
    {        
        type: "stackedArea",//column area line stackedArea stackedColumn
        xValueType: "dateTime",
        color: "#CCCC00",
        showInLegend: true,
        legendText: "Radiacion",
        dataPoints: datos.canal0
    },{        
        type: "line",
        xValueType: "dateTime",
        axisYType: "secondary",
        showInLegend: true,
        legendText: "Temperatura",        
        dataPoints: datos.canal1
    }
    ]
	});

    chart.render();
}

function calcularAreaBajoLaCurva(datos){
    var anterior = null;
    var area = 0;
    var promedio = 0;
    for (key in datos) {
        //datos[key].x = datos[key].x / 1000;
        if (anterior != null){
            area = area + (((datos[key].x - anterior.x)/1000) * ((datos[key].y + anterior.y) / 2));
            console.log(datos[key].x - anterior.x);
        }
        anterior = datos[key];
        promedio = promedio + datos[key].y;
    }
    promedio = promedio / datos.length;
    $("#resultado").html('Puntos: ' + datos.length + '<br>Altura Promedio: ' + parseInt(promedio) + '<br>Area: ' + parseInt(area));
}

function cleanString(aString){
return String(aString).replace(/(\r\n|\n|\r)/gm, "");
}