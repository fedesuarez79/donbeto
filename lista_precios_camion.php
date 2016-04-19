<?php
session_start();
echo "<meta http-equiv='Content-type' content='text/html;charset=utf-8' />";

echo "<style>";
echo "table {";
echo "    border-collapse: collapse;";
echo "}";
echo "table, td, th {";
echo "    border: 1px solid black;";
echo "}";
echo "</style>";

$conn = mysql_connect("localhost","fedesuarez","ambrosio"); 
mysql_set_charset("utf8");

mysql_select_db("donbeto",$conn);

date_default_timezone_set("America/Argentina/Catamarca");



$sql = "select nombre, categoria_id from categorias";
$result=mysql_query($sql,$conn) or die(mysql_error());
while ($fila = mysql_fetch_object($result)) {
  echo "<h3>".$fila->nombre."</h3>";
  echo "<table border='1'><tr>";
  echo "<td align='center'>Código de barras</td>";
  echo "<td align='center'>Código Don Beto</td>";
  echo "<td align='center' width='60%'>Descripción</td>";
  echo "<td align='center'>Unidad</td>";
  echo "<td align='center'>Precio</td>";
  echo "</tr>";


  $sql = "select descripcion, unidad, precio_venta, codigo_barras, codigo_nuestro, depende_id, fraccion, marcado, obsoleto, stock from items where categoria=".$fila->categoria_id." order by descripcion";
  $result2=mysql_query($sql,$conn) or die(mysql_error());
  while ($fila2 = mysql_fetch_object($result2)) {
    if(!$fila2->obsoleto){
      echo "<tr><td align='center'>".$fila2->codigo_barras."</td>";
      echo "<td align='center'>".$fila2->codigo_nuestro."</td>";
      echo "<td>".$fila2->descripcion."</td>";
      echo "<td align='center'>".$fila2->unidad."</td>";

      if($fila2->precio_venta>50){
	$fila2->precio_venta=round($fila2->precio_venta*1.05,0);
      }else if($fila2->precio_venta>1){
	$fila2->precio_venta=round($fila2->precio_venta*1.05,1);
      }else{
	$fila2->precio_venta=round($fila2->precio_venta*1.05,4);
      }
      if(!$fila2->depende_id){
	if(!$fila2->precio_venta){
	  echo "<td align='center'>Consultar</td></tr>";
	}else{
	  echo "<td align='center'>$".$fila2->precio_venta."</td></tr>";
	}
      }else{
	//echo "hola ".$fila2->descripcion." ".$fila2->depende_id;
	$sql = "select descripcion, costo_sin_iva, iva, marcado from items where item_id=".$fila2->depende_id;
	$result3=mysql_query($sql,$conn) or die(mysql_error());
	$fila3 = mysql_fetch_row($result3);
	//echo "hola ".$fila3[0]." ".$fila3[1]." ".$fila3[2]." ".(1+$fila2->marcado)." ".$fila2->fraccion." ".$fila2->descripcion."<br>";
	echo "<td align='center'>$".round(($fila3[1]*$fila3[2]*(1+$fila2->marcado)/$fila2->fraccion)*1.05,3)."</td></tr>";
	
      
      }
    }

  }


  echo "</table>";

}










?>