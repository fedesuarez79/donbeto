<meta http-equiv="Content-type" content="text/html;charset=utf-8" />

<?php

   echo "Hola Batita<br><br>";
   echo "<br><br>";
$servername = "localhost";
$username = "fedesuarez";
$password = "ambrosio";

$conn = mysql_connect("localhost","fedesuarez","ambrosio"); 
mysql_set_charset("utf8");

$handle = @fopen("data_entry_20160119.txt", "r");
mysql_select_db("donbeto",$conn);





while (!feof($handle)) // Loop til end of file.
{
$buffer = fgets($handle, 4096);
 // Read a line.
list($a,$b,$c,$d,$e,$f,$g,$h,$i,$j,$k,$l,$m,$n)=explode("\t",$buffer);
//Separate string by the means of \t
if($e==null)$e=0;
if($f==null)$f=0;
if($g==null)$g=0;
if($j==null)$j=0;
$k="2015-12-30";
if($l==null)$l=0;
$m="2015-06-15";

//echo $a." - ".$b." - ".$c." - ".$d." - ".$e." - ".$f." - ".$g." - ".$h." - ".$i." - ".$j." - ".$k." - ".$l." - ".$m." - ".$n."<br>";
$sql = "INSERT INTO items (codigo_barras, codigo_nuestro, descripcion, unidad, costo_sin_iva, costo_con_iva, marcado, precio_venta, condicion_mayorista, descuento_mayorista, ultima_modif_precio, stock, fecha_compra, categoria) VALUES(\"".$a."\",\"".$b."\",\"".$c."\",\"".$d."\",\"".$e."\",\"".$f."\",\"".$g."\",\"".$h."\",\"".$i."\",\"".$j."\",\"".$k."\",\"".$l."\",\"".$m."\",\"".$n."\")";   
echo $sql."<br>";
mysql_query($sql,$conn) or die(mysql_error());
}



?>


