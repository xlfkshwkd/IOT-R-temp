<?php
$servername = "localhost";
// REPLACE with your Database name
$dbname = "dht11";
// REPLACE with Database user
$username = "root";
// REPLACE with Database user password
$password = "";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
 die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT seq, time_measured, temperature, humidity FROM dhtdata order by
time_measured desc limit 40";
$result = $conn->query($sql);
while ($data = $result->fetch_assoc()){
 $sensor_data[] = $data;
}
$readings_time = array_column($sensor_data, 'time_measured');
// ******* Uncomment to convert readings time array to your timezone ********
$i = 0;
foreach ($readings_time as $reading){
 // Uncomment to set timezone to - 1 hour (you can change 1 to any number)
 $readings_time[$i] = date("Y-m-d H:i:s", strtotime("$reading - 1 hours"));
 // Uncomment to set timezone to + 4 hours (you can change 4 to any number)
 //$readings_time[$i] = date("Y-m-d H:i:s", strtotime("$reading + 4 hours"));
 $i += 1;
}
$value1 = json_encode(array_reverse(array_column($sensor_data, 'temperature')),
JSON_NUMERIC_CHECK);
$value2 = json_encode(array_reverse(array_column($sensor_data, 'humidity')),
JSON_NUMERIC_CHECK);
$reading_time = json_encode(array_reverse($readings_time), JSON_NUMERIC_CHECK);
/*echo $value1;
echo $value2;
echo $value3;
echo $reading_time;*/
$result->free();
$conn->close();
?>
<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">
 <script src="https://code.highcharts.com/highcharts.js"></script>
 <style>
 body {
 min-width: 310px;
max-width: 1280px;
 height: 500px;
 margin: 0 auto;
 }
 h2 {
 font-family: Arial;
 font-size: 2.5rem;
 text-align: center;
 }
 </style>
 <body>
 <h2> </h2> 실험실 온도와 습도
 <div id="chart-temperature" class="container"></div>
 <div id="chart-humidity" class="container"></div>
<script>
var value1 = <?php echo $value1; ?>;
var value2 = <?php echo $value2; ?>;
var reading_time = <?php echo $reading_time; ?>;
var chartT = new Highcharts.Chart({
 chart:{ renderTo : 'chart-temperature' },
 title: { text: 'DHT11 ' }, 센서 온도
 series: [{
 showInLegend: false,
 data: value1 }],
 plotOptions: {
 line: { animation: false,
 dataLabels: { enabled: true }
 },
 series: { color: '#059e8a' }
 },
 xAxis: {
 type: 'datetime',
 categories: reading_time
 },
 yAxis: {
 title: { text: 'Temperature (Celsius)' }
 //title: { text: 'Temperature (Fahrenheit)' }
 },
 credits: { enabled: false }
});
var chartH = new Highcharts.Chart({
 chart:{ renderTo:'chart-humidity' },
 title: { text: 'DHT11 ' }, 센서 습도
 series: [{
 showInLegend: false,
 data: value2 }],
 plotOptions: {
 line: { animation: false,
 dataLabels: { enabled: true }
 }
 },
 xAxis: {
 type: 'datetime',
 //dateTimeLabelFormats: { second: '%H:%M:%S' },
 categories: reading_time
 },
 yAxis: {
 title: { text: 'Humidity (%)' }
 },
 credits: { enabled: false }
});
var chartP = new Highcharts.Chart({
 chart:{ renderTo:'chart-pressure' },
 title: { text: 'BME280 Pressure' },
 series: [{
 showInLegend: false,
 data: value3
 }],
 plotOptions: {
 line: { animation: false,
 dataLabels: { enabled: true }
 },
 series: { color: '#18009c' }
 },
 xAxis: {
 type: 'datetime',
 categories: reading_time
 },
 yAxis: {
 title: { text: 'Pressure (hPa)' }
 },
 credits: { enabled: false }
});
</script>
</body>
</html>