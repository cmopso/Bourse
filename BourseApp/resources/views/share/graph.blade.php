<!-- Styles -->
<style>
#chartdiv {
  width: 100%;
  height: 650px;
}
</style>

<!-- Resources -->
<script src="https://www.amcharts.com/lib/4/core.js"></script>
<script src="https://www.amcharts.com/lib/4/charts.js"></script>
<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>

<!-- Chart code -->
<script>
am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

// Create chart instance
var chart = am4core.create("chartdiv", am4charts.XYChart);
chart.padding(0, 15, 0, 15);
chart.colors.step = 3;
chart.leftAxesContainer.layout = "vertical";

// Add data
chart.dateFormatter.dateFormat = "yyyy-MM-dd";
chart.data = generateChartData();

// Create axes
var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
dateAxis.renderer.minGridDistance = 50;
dateAxis.minZoomCount = 31;
//dateAxis.renderer.minGridDistance = 30;
dateAxis.periodChangeDateFormats.setKey("month", "[bold]yyyy");
dateAxis.periodChangeDateFormats.setKey("week", "[bold]MMM dd");
dateAxis.periodChangeDateFormats.setKey("day", "[bold]MMM dd");

dateAxis.renderer.grid.template.location = 0;
dateAxis.renderer.ticks.template.length = 8;
dateAxis.renderer.ticks.template.strokeOpacity = 0.1;
//dateAxis.renderer.grid.template.disabled = true;
dateAxis.renderer.ticks.template.disabled = false;
dateAxis.renderer.ticks.template.strokeOpacity = 0.2;
dateAxis.renderer.minLabelPosition = 0.01;
dateAxis.renderer.maxLabelPosition = 0.99;

dateAxis.minHeight = 30;
dateAxis.groupData = true;

// first Y axis
var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
//valueAxis.renderer.opposite = true;
valueAxis.height = am4core.percent(65);

valueAxis.renderer.gridContainer.background.fill = am4core.color("#000000");
valueAxis.renderer.gridContainer.background.fillOpacity = 0.05;
valueAxis.renderer.inside = false;
valueAxis.renderer.labels.template.verticalCenter = "bottom";
valueAxis.renderer.labels.template.padding(5, 5, 5, 5);
//valueAxis.renderer.maxLabelPosition = 0.95;
valueAxis.renderer.fontSize = "0.8em"

// Create series
// Prices
var series1 = chart.series.push(new am4charts.LineSeries());
series1.dataFields.valueY = "prices";
series1.dataFields.dateX = "date";
series1.strokeWidth = 2;
series1.stroke = am4core.color("#5499C7");
series1.minBulletDistance = 10;
series1.tooltipText = "{valueY}";
series1.tooltip.pointerOrientation = "vertical";
series1.tooltip.background.cornerRadius = 20;
series1.tooltip.background.fillOpacity = 0.5;
series1.tooltip.label.padding(12,12,12,12);
//series1.dataFields.valueYShow = "changePercent";
//series1.tooltipText = "[b]{valueY.changePercent}[/]%";

// Min
var series2 = chart.series.push(new am4charts.StepLineSeries());
series2.dataFields.valueY = "min";
series2.dataFields.dateX = "date";
series2.strokeWidth = 2;
series2.stroke = am4core.color("#AAAAAA");
series2.minBulletDistance = 10;
series2.tooltipText = "{valueY}";
series2.tooltip.pointerOrientation = "vertical";
series2.tooltip.background.cornerRadius = 20;
series2.tooltip.background.fillOpacity = 0.5;
series2.tooltip.label.padding(12,12,12,12);
//series2.dataFields.valueYShow = "changePercent";
//series2.tooltipText = "[b]{valueY.changePercent}[/]%";

// Max
var series3 = chart.series.push(new am4charts.StepLineSeries());
series3.dataFields.valueY = "max";
series3.dataFields.dateX = "date";
series3.strokeWidth = 2;
series3.stroke = am4core.color("#AAAAAA");
series3.minBulletDistance = 10;
series3.tooltipText = "{valueY}";
series3.tooltip.pointerOrientation = "vertical";
series3.tooltip.background.cornerRadius = 20;
series3.tooltip.background.fillOpacity = 0.5;
series3.tooltip.label.padding(12,12,12,12);
//series3.dataFields.valueYShow = "changePercent";
//series3.tooltipText = "[b]{valueY.changePercent}[/]%";

// Buy
var series4 = chart.series.push(new am4charts.ColumnSeries());
series4.dataFields.valueY = "Achat";
series4.dataFields.dateX = "date";
series4.strokeWidth = 4;
series4.stroke = am4core.color("#2980B9");
series4.fill = am4core.color("#2980B9");
series4.minBulletDistance = 10;
series4.tooltipText = "{valueY}";
series4.tooltip.pointerOrientation = "vertical";
series4.tooltip.background.cornerRadius = 20;
series4.tooltip.background.fillOpacity = 0.5;
series4.tooltip.label.padding(12,12,12,12);
//series4.dataFields.valueYShow = "changePercent";
//series4.tooltipText = "[b]{valueY.changePercent}[/]%";

// Sale
var series5 = chart.series.push(new am4charts.ColumnSeries());
series5.dataFields.valueY = "Vente";
series5.dataFields.dateX = "date";
series5.strokeWidth = 4;
series5.stroke = am4core.color("#884EA0");
series5.fill = am4core.color("#884EA0");
series5.minBulletDistance = 10;
series5.tooltipText = "{valueY}";
series5.tooltip.pointerOrientation = "vertical";
series5.tooltip.background.cornerRadius = 20;
series5.tooltip.background.fillOpacity = 0.5;
series5.tooltip.label.padding(12,12,12,12);
//series5.dataFields.valueYShow = "changePercent";
//series5.tooltipText = "[b]{valueY.changePercent}[/]%";

// CAC
//var series6 = chart.series.push(new am4charts.LineSeries());
//series6.dataFields.valueY = "cac";
//series6.dataFields.dateX = "date";
//series6.strokeWidth = 2;
//series6.stroke = am4core.color("#000000");
//series6.minBulletDistance = 10;
//series6.tooltipText = "{valueY}";
//series6.tooltip.pointerOrientation = "vertical";
//series6.tooltip.background.cornerRadius = 20;
//series6.tooltip.background.fillOpacity = 0.5;
//series6.tooltip.label.padding(12,12,12,12);
//series6.dataFields.valueYShow = "changePercent";
//series6.tooltipText = "[b]{valueY.changePercent}[/]%";

//valueAxis.renderer.labels.template.adapter.add("text", function(text) { return text + "%";});

// Add scrollbar
//chart.scrollbarX = new am4charts.XYChartScrollbar();
//chart.scrollbarX.series.push(series1);
//chart.scrollbarX.parent = chart.bottomAxesContainer;
chart.zoomOutButton.align = "left";

// Add cursor
chart.cursor = new am4charts.XYCursor();
chart.cursor.xAxis = dateAxis;
chart.cursor.snapToSeries = series1;

// these two lines makes the axis to be initially zoomed-in
var startDate = new Date("2018-01-01");
var endDate = new Date();
var NbMonth = endDate.getMonth() - startDate.getMonth();
var NbYear = endDate.getFullYear() - startDate.getFullYear();
var ratio6Month = 6/ (NbYear * 12 + NbMonth);
dateAxis.start = 1 - ratio6Month;
dateAxis.keepSelection = true;



// second chart with dedicated Axis
var valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
valueAxis2.tooltip.disabled = true;
// height of axis
valueAxis2.height = am4core.percent(35);
valueAxis2.zIndex = 3
// this makes gap between panels
valueAxis2.marginTop = 30;
valueAxis2.renderer.baseGrid.disabled = true;
valueAxis2.renderer.inside = true;
valueAxis2.renderer.labels.template.verticalCenter = "bottom";
valueAxis2.renderer.labels.template.padding(2, 2, 2, 2);
//valueAxis.renderer.maxLabelPosition = 0.95;
valueAxis2.renderer.fontSize = "0.8em";

valueAxis2.renderer.gridContainer.background.fill = am4core.color("#000000");
valueAxis2.renderer.gridContainer.background.fillOpacity = 0.05;
//valueAxis2.renderer.opposite = true;

valueAxis2.renderer.labels.template.adapter.add("text", function(text) { return text + "%";});

// CAC
var series6 = chart.series.push(new am4charts.LineSeries());
series6.dataFields.valueY = "cac";
series6.dataFields.dateX = "date";
series6.strokeWidth = 2;
series6.stroke = am4core.color("#000000");
series6.minBulletDistance = 10;
series6.tooltipText = "{valueY}";
series6.tooltip.pointerOrientation = "vertical";
series6.tooltip.background.cornerRadius = 20;
series6.tooltip.background.fillOpacity = 0.05;
series6.tooltip.label.padding(12,12,12,12);
series6.dataFields.valueYShow = "changePercent";
series6.tooltipText = "[b]{valueY.changePercent}[/]%";
series6.yAxis = valueAxis2;

// Price
var series7 = chart.series.push(new am4charts.LineSeries());
series7.dataFields.valueY = "prices";
series7.dataFields.dateX = "date";
series7.strokeWidth = 2;
series7.stroke = am4core.color("#5499C7");
series7.minBulletDistance = 10;
series7.tooltipText = "{valueY}";
series7.tooltip.pointerOrientation = "vertical";
series7.tooltip.background.cornerRadius = 20;
series7.tooltip.background.fillOpacity = 0.5;
series7.tooltip.label.padding(12,12,12,12);
series7.dataFields.valueYShow = "changePercent";
series7.tooltipText = "[b]{valueY.changePercent}[/]%";
series7.yAxis = valueAxis2;


function generateChartData() {
    var chartData = [];
    //var firstDate = new Date();
    //firstDate.setDate(firstDate.getDate() - 508);
    //i =0;
    @foreach ($priceShareData["price"] as $key => $priceShare)
        chartData.push({
            date: "{{ $key }}",
            prices: {{ $priceShare }},
            @if (isset($priceShareData["min"][$key]))
                min: {{ $priceShareData["min"][$key] }},
                max: {{ $priceShareData["max"][$key] }},
            @endif
            @if (isset($priceShareData["Achat"][$key]))
                Achat: {{ $priceShareData["Achat"][$key] }},
            @endif
            @if (isset($priceShareData["Vente"][$key]))
                Vente: {{ $priceShareData["Vente"][$key] }},
            @endif
            @if (isset($priceShareData["cac"][$key]))
                cac: {{ $priceShareData["cac"][$key] }},
            @endif
            });
    @endforeach

    return chartData;
}

/**
 * Set up external controls
 */
var inputFieldFormat = "yyyy-MM-dd";

document.getElementById("b1m").addEventListener("click", function() {
  resetButtonClass();
  var date = new Date(dateAxis.max);
  date.setMonth(date.getMonth() - 1);
  dateAxis.zoomToDates(date, new Date(dateAxis.max));
  //this.className = "amcharts-input amcharts-input-selected";
});

document.getElementById("b3m").addEventListener("click", function() {
  resetButtonClass();
  var date = new Date(dateAxis.max);
  date.setMonth(date.getMonth() - 3);
  dateAxis.zoomToDates(date, new Date(dateAxis.max));
  //this.className = "amcharts-input amcharts-input-selected";
});

document.getElementById("b6m").addEventListener("click", function() {
  resetButtonClass();
  var date = new Date(dateAxis.max);
  date.setMonth(date.getMonth() - 6);
  dateAxis.zoomToDates(date, new Date(dateAxis.max));
  //this.className = "amcharts-input amcharts-input-selected";
});

document.getElementById("b1y").addEventListener("click", function() {
  resetButtonClass();
  var date = new Date(dateAxis.max);
  date.setFullYear(date.getFullYear() - 1);
  dateAxis.zoomToDates(date, new Date(dateAxis.max));
  //this.className = "amcharts-input amcharts-input-selected";
});

document.getElementById("bytd").addEventListener("click", function() {
  resetButtonClass();
  var date = new Date(dateAxis.max);
  date.setMonth(0, 1);
  date.setHours(0, 0, 0, 0);
  var endDate = new Date(dateAxis.max);
  dateAxis.zoomToDates(date, new Date(dateAxis.max));
  //this.className = "amcharts-input amcharts-input-selected";
});

document.getElementById("bmax").addEventListener("click", function() {
  resetButtonClass();
  dateAxis.zoom({start:0, end:1});
  //this.className = "amcharts-input amcharts-input-selected";
});

function resetButtonClass() {
  var selected = document.getElementsByClassName("amcharts-input-selected");
  for(var i = 0; i < selected.length; i++) {
    selected[i].className = "amcharts-input";
  }
}

dateAxis.events.on("selectionextremeschanged", function() {
  updateFields();
});

dateAxis.events.on("extremeschanged", updateFields);

function updateFields() {
  var minZoomed = dateAxis.minZoomed + am4core.time.getDuration(dateAxis.mainBaseInterval.timeUnit, dateAxis.mainBaseInterval.count) * 0.5;
  document.getElementById("fromfield").value = chart.dateFormatter.format(minZoomed, inputFieldFormat);
  document.getElementById("tofield").value = chart.dateFormatter.format(new Date(dateAxis.maxZoomed), inputFieldFormat);
}

document.getElementById("fromfield").addEventListener("keyup", updateZoom);
document.getElementById("tofield").addEventListener("keyup", updateZoom);

var zoomTimeout;
function updateZoom() {
  if (zoomTimeout) {
    clearTimeout(zoomTimeout);
  }
  zoomTimeout = setTimeout(function() {
    resetButtonClass();
    var start = document.getElementById("fromfield").value;
    var end = document.getElementById("tofield").value;
    if ((start.length < inputFieldFormat.length) || (end.length < inputFieldFormat.length)) {
      return;
    }
    var startDate = chart.dateFormatter.parse(start, inputFieldFormat);
    var endDate = chart.dateFormatter.parse(end, inputFieldFormat);

    if (startDate && endDate) {
      dateAxis.zoomToDates(startDate, endDate);
    }
  }, 500);
}

}); // end am4core.ready()
</script>

<!-- HTML -->
<div id="controls" style="width: 100%; overflow: hidden;">
    <div style="float: left; margin-left: 15px;">
        From: <input type="text" id="fromfield" class="amcharts-input" />
        To: <input type="text" id="tofield" class="amcharts-input" />
    </div>
    <div style="float: right; margin-right: 15px;">
        <!-- button id="bpc" class="amcharts-input btn btn-danger">%</button -->
        <button id="bpr" class="amcharts-input btn btn-success"><</button>
        <button id="baf" class="amcharts-input btn btn-success">></button>
        <button id="b1m" class="amcharts-input btn btn-primary">1m</button>
        <button id="b3m" class="amcharts-input btn btn-primary">3m</button>
        <button id="b6m" class="amcharts-input btn btn-primary">6m</button>
        <button id="b1y" class="amcharts-input btn btn-primary">1y</button>
        <button id="bytd" class="amcharts-input btn btn-primary">YTD</button>
        <button id="bmax" class="amcharts-input btn btn-primary">MAX</button>
    </div>
</div>

<div id="chartdiv"></div>