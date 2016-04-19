<meta http-equiv="Content-type" content="text/html;charset=utf-8" />

<?php

$servername = "localhost";
$username = "fedesuarez";
$password = "ambrosio";

$conn = mysql_connect("localhost","fedesuarez","ambrosio"); 
mysql_set_charset("utf8");

$handle = @fopen("nuevos_items.txt", "r");
mysql_select_db("donbeto",$conn);

date_default_timezone_set("America/Argentina/Catamarca");




while (!feof($handle)) // Loop til end of file.
{
  $buffer = fgets($handle, 4096);
  // Read a line.
  list($descripcion,$unidad,$costo_con_iva,$marcado,$cantidad,$categoria,$codigo_barras)=explode("\t",$buffer);
  //Separate string by the means of \t

  if($descripcion){
    $fecha=date("Y-m-d");
    $iva=1.21;
    $costo_sin_iva=$costo_con_iva/$iva;
    $precio_venta=$costo_con_iva*(1+$marcado);
    $pag_iva=1;
    if($categoria==25){
	$proveedor_id=14;
    }else if($categoria==26){  	
    	$proveedor_id=15;
    }else{
    	$proveedor_id=13;
	}
    
    $sql = "select codigo_nuestro from items order by codigo_nuestro";
    $result=mysql_query($sql,$conn) or die(mysql_error());
    while ($fila = mysql_fetch_object($result)) {
      $id_nuevo=$fila->codigo_nuestro;
    }
    $codigo_nuestro=$id_nuevo+1;


    $sql = "INSERT INTO items (codigo_barras, codigo_nuestro, descripcion, unidad, costo_sin_iva, costo_con_iva, marcado, precio_venta, stock, fecha_compra, categoria, proveedor_id, iva, pag_iva) VALUES(\"".$codigo_barras."\",\"".$codigo_nuestro."\",\"".$descripcion."\",\"".$unidad."\",\"".$costo_sin_iva."\",\"".$costo_con_iva."\",\"".$marcado."\",\"".$precio_venta."\",\"".$cantidad."\",\"".$fecha."\",\"".$categoria."\",\"".$proveedor_id."\",\"".$iva."\",\"".$pag_iva."\")";   
    echo $sql."<br>";
    mysql_query($sql,$conn) or die(mysql_error());
    
    $sql="select item_id from items where descripcion='".$descripcion."' and codigo_nuestro='".$codigo_nuestro."'";
    $result=mysql_query($sql,$conn) or die(mysql_error());
    $fila4=mysql_fetch_row($result);
    $sql="insert into histo_cargas set item_id=$fila4[0], cantidad=".$cantidad.", costo_sin_iva=".$costo_sin_iva.", marcado=".$marcado.", precio_venta=".$precio_venta;
    echo $sql."<br><br>";
    $result=mysql_query($sql,$conn) or die(mysql_error());
  }
}



?>


