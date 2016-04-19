<meta http-equiv="Content-type" content="text/html;charset=utf-8" />

<?php


$conn = mysql_connect("localhost","fedesuarez","ambrosio"); 
mysql_set_charset("utf8");

$handle = @fopen("ventas_entry_20160127.txt", "r");
mysql_select_db("donbeto",$conn);




$fecha_anterior="2015-06-15";
while (!feof($handle)) // Loop til end of file.
{
$buffer = fgets($handle, 4096);
 // Read a line.
list($a,$b,$c,$d)=explode("\t",$buffer);
//Separate string by the means of \t
$d=trim($d);
$d=str_replace(' ','',$d);
if($a==null){
$a=$fecha_anterior;
}else{
  $fecha_anterior=$a;
}
if($d!=null){
  //echo $a." - ".$b." - ".$c." - ".$d."<br>";
  $sql = "select item_id, precio_venta from items where codigo_barras='".$d."' or codigo_nuestro='".$d."'";
  $result=mysql_query($sql,$conn) or die(mysql_error());
  $row = mysql_fetch_object($result); 
  if(!$row->item_id){echo "<font color='red'>No se encontró venta código=$d</font><br>";}
  else{
    echo "Ingresando venta $a código=".$d.": ".$row->item_id." ";
    echo $row->precio_venta."<br>";
    $sql = "INSERT INTO ventas (item_id, valor_venta, fecha, cantidad) VALUES(\"".$row->item_id."\",\"".$row->precio_venta*$b/1.35."\",\"".$a."\",\"".$b."\")"; 
    echo $sql."<br>";     
    $result=mysql_query($sql,$conn) or die(mysql_error());
    $sql = "update items set stock=stock-".$b." where item_id=".$row->item_id;
    echo $sql."<br>";     
    $result=mysql_query($sql,$conn) or die(mysql_error());

  }
 


}else if($c!=null){
  echo "<font color='blue'>por descripción: ".$c."</font><br>";
  $sql = "select item_id, precio_venta from items where descripcion like '%".$c."%'";
  //echo $sql."<br>";
  $result=mysql_query($sql,$conn) or die(mysql_error());
  $row = mysql_fetch_object($result); 
  $rowCount = mysql_num_rows($result);

  if($rowCount!=1){
    if($rowCount==0){echo "<font color='red'>No se encontró venta descripción: $c</font><br>";}
    else{echo "<font color='red'>Resultado múltiple encontrado</font><br>";}
  }
  else{
    echo "<font color=green>Ingresando venta $a descripcion=".$c.": ".$row->item_id." </font>";
    echo $row->precio_venta."<br>";
    $sql = "INSERT INTO ventas (item_id, valor_venta, fecha, cantidad) VALUES(\"".$row->item_id."\",\"".$row->precio_venta*$b/1.35."\",\"".$a."\",\"".$b."\")";  
    //$result=mysql_query($sql,$conn) or die(mysql_error());
    echo $sql."<br>";     
    $sql = "update items set stock=stock-".$b." where item_id=".$row->item_id;
    echo $sql."<br>";     
    $result=mysql_query($sql,$conn) or die(mysql_error());


  }
}

}




?>