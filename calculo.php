<?php
session_start();
echo "<meta http-equiv='Content-type' content='text/html;charset=utf-8' />";

/*siiiii???*/

$conn = mysql_connect("localhost","fedesuarez","ambrosio"); 
mysql_set_charset("utf8");

mysql_select_db("donbeto",$conn);

date_default_timezone_set("America/Argentina/Catamarca");



$sql = "select * from ventas where fecha='2016-04-17'";
$result=mysql_query($sql,$conn) or die(mysql_error());
$total_ganancia=0;
$total_costo=0;
$total_venta=0;
while ($fila = mysql_fetch_object($result)) {
  $sql = "select costo_con_iva from items where item_id=".$fila->item_id;
  $result2=mysql_query($sql,$conn) or die(mysql_error());
  $fila2 = mysql_fetch_row($result2);
  $costo=$fila2[0]*$fila->cantidad;
  $ganancia=$fila->valor_venta-$costo;
  echo "Venta=".$fila->valor_venta." Costo=$costo Ganancia=$ganancia<br>";
  $total_venta=$total_venta+$fila->valor_venta;
  $total_costo=$total_costo+$costo;
  $total_ganancia=$total_ganancia+$ganancia;
}
echo "<br>Total venta = $total_venta<br>Total costo = $total_costo<br>Total ganancia = $total_ganancia<br>";


?>




