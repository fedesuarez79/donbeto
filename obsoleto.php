<?php
session_start();
echo "<meta http-equiv='Content-type' content='text/html;charset=utf-8' />";


$conn = mysql_connect("localhost","fedesuarez","ambrosio"); 
mysql_set_charset("utf8");

mysql_select_db("minimarket",$conn);


echo "<h3>Carga de stock:</h3>";
echo "<table><tr><td>";
echo "<form action='obsoleto.php' method='post'>";

    $sql = "select categoria_id, nombre from categorias";
    $result=mysql_query($sql,$conn) or die(mysql_error());
    echo "<select name='categoria'>";
    echo "<option value='null'>Categoria</option>";
    while($fila = mysql_fetch_object($result)){
      if($fila->categoria_id)echo "<option value='$fila->categoria_id'>$fila->nombre</option>";
    }
    echo "</select>";

echo" <input type='submit' value='Seleccionar'>";
echo" </form></td>";
echo "</tr></table>";


if($_POST["categoria"]){
  echo "<form action='obsoleto.php' method='post'>";

  echo "<table border='1' style='width:100%'><tr><td align='center' style='width:5%'>Item id</td><td align='center' style='width:10%'>Código nuestro</td><td align='center' style='width:65%'>Descripción</td><td align='center' style='width:6%'>Stock</td><td align='center' style='width:6%'>Obs</td></tr>";

  $sql="select item_id, codigo_nuestro, descripcion, stock, obsoleto from items where categoria=".$_POST["categoria"]." order by descripcion";
  $result=mysql_query($sql,$conn) or die(mysql_error());
  while($fila = mysql_fetch_object($result)){
    echo "<tr><td align='center'>".$fila->item_id."</td><td align='center'>".$fila->codigo_nuestro."</td><td>".$fila->descripcion."</td><td align='center'><input type='text' name='cantidad[]' value='".$fila->stock."' style='width: 40px'></td><td align='center'><input type='text' name='obsoleto[]' value='".$fila->obsoleto."' style='width: 40px'></td></tr>";
  echo "<input type='hidden' name='id[]' value='".$fila->item_id."'>";
    

  }
  echo"</table>";
  echo "<input type='hidden' name='flag_cerrar' value='1'>";
  echo "<br><input type='submit' value='Cargar stock'>";
  echo" </form>";
}


/************************/
/*    CERRAR VENTA      *
/************************/

if($_POST['flag_cerrar']){
  date_default_timezone_set("America/Argentina/Catamarca");
  foreach($_POST['id'] as $key=>$valor){
    
    $sql="update items set stock=".$_POST['cantidad'][$key].", obsoleto=".$_POST['obsoleto'][$key]." where item_id=$valor";
    echo $sql."<br>";
    $result=mysql_query($sql,$conn) or die(mysql_error());

    if(!$_POST['obsoleto'][$key]){
      $sql="select costo_sin_iva, marcado, precio_venta from items where item_id=$valor";
      $result=mysql_query($sql,$conn) or die(mysql_error());
      $fila = mysql_fetch_row($result);
      $sql="insert into histo_cargas set item_id=$valor, cantidad=".$_POST['cantidad'][$key].", costo_sin_iva=$fila[0], marcado=$fila[1], precio_venta=$fila[2], fecha='".date("Y-m-d")."', hora='".date("H:i:s")."'";
      echo $sql."<br>";   
      $result=mysql_query($sql,$conn) or die(mysql_error());
    }
  }
  

}

?>