<?php
session_start();
echo "<meta http-equiv='Content-type' content='text/html;charset=utf-8' />";


$conn = mysql_connect("localhost","fedesuarez","ambrosio"); 
mysql_set_charset("utf8");

mysql_select_db("donbeto",$conn);

date_default_timezone_set("America/Argentina/Catamarca");



if($_POST["limpiar"]==1){
unset($_SESSION['acumulado_ventas']);
}





/******************/
/*    BUSQUEDA    *
/******************/


echo "<h3>Búsqueda:</h3>";
echo "<form action='carga2.php' method='post'>";
echo "<input type='text' name='name' autofocus='autofocus' value=''>";
echo "<input type='hidden' name='flag_busqueda' value='1'>";
echo" <input type='submit' value='Buscar'>";
echo" </form></body></html>";

if($_POST["name"]){
    echo "<form action='carga2.php' method='post'>";
    echo "<table border='1' style='width:80%'><tr><td align='center'>Descripción</td><td align='center'>Unidad</td><td align='center'>Costo sin IVA</td><td align='center'>IVA</td><td align='center'>Costo con IVA</td><td align='center'>%</td><td align='center'>Precio venta</td><td align='center'>Stock</td><td align='center'>Categoría</td><td align='center'>Depende id</td><td align='center'>Fracción dependencia</td><td align='center'>Código barras</td><td align='center'>Item id</td><td align='center'>paga IVA</td><td align='center'>Código nuestro</td><td align='center'>Fecha compra</td><td align='center'>Código proveedor</td><td align='center'>Prov id</td></tr>";


  $busqueda=$_POST["name"];
  $sql = "select * from items where codigo_barras='$busqueda' or codigo_nuestro='$busqueda' or descripcion like '%$busqueda%'";
  $result=mysql_query($sql,$conn) or die(mysql_error());
  while ($fila = mysql_fetch_object($result)) {
	echo "<tr>";
	echo "<td><input type='text' name='desc' value='$fila->descripcion' style='width: 120px;'</td>";
	echo "<td><input type='text' name='uni' value='$fila->unidad' style='width: 40px;'</td>";
	echo "<td><input type='text' name='cosi' value='$fila->costo_sin_iva' style='width: 40px;'</td>";
	if(!$fila->iva){
	  echo "<td><input type='text' name='iva' value='1.21' style='width: 40px;'</td>";
	}else{
	  echo "<td><input type='text' name='iva' value='$fila->iva' style='width: 40px;'</td>";
	}
	echo "<td><input type='text' name='cciv' value='$fila->costo_con_iva' style='width: 40px;'</td>";
	echo "<td><input type='text' name='porc' value='$fila->marcado' style='width: 25px;'</td>";
	echo "<td><input type='text' name='prve' value='$fila->precio_venta' style='width: 40px;'</td>";
	echo "<td><input type='text' name='sto' value='$fila->stock' style='width: 40px;'</td>";
	echo "<td><input type='text' name='cat' value='$fila->categoria' style='width: 60px;'</td>";
	echo "<td><input type='text' name='depi' value='$fila->depende_id' style='width: 40px;'</td>";
	echo "<td><input type='text' name='frac' value='$fila->fraccion' style='width: 40px;'</td>";
	echo "<td><input type='text' name='codb' value='$fila->codigo_barras' style='width: 40px;'</td>";
	echo "<td><input type='text' name='itid' value='$fila->item_id' style='width: 40px;'</td>";
	echo "<td><input type='text' name='piva' value='$fila->pag_iva' style='width: 40px;'</td>";
	echo "<td>$fila->codigo_nuestro</td>";
	echo "<td>".date('Y-m-d')."</td>";
	echo "<td>$fila->codigo_proveedor</td>";
	echo "<td>$fila->proveedor_id</td>";
	echo "</tr>"; 



  }
  echo "</table>";
  echo "<input type='hidden' name='flag_cerrar' value='1'>";
  echo "<br><br><input type='submit' value='Terminar carga' style='font-size: 2em'>";
  echo" </form>";


}else{
}

/************************/
/*    CERRAR VENTA      *
/************************/

if($_POST['flag_cerrar']){

  $iva=$_POST['iva'];
  $sql="select costo_con_iva, costo_sin_iva, precio_venta from items where item_id=".$_POST['itid'];
  $result=mysql_query($sql,$conn) or die(mysql_error());
  $fila = mysql_fetch_row($result);

  if($fila[2]!=$_POST['prve']){
    $precio_venta=$_POST['prve'];
    $costo_con_iva=$precio_venta/($_POST['porc']+1);
    $costo_sin_iva=$costo_con_iva/$iva;
  }else{
    if($fila[0]!=$_POST['cciv']){
      $costo_sin_iva=$_POST['cciv']/$iva;
      $costo_con_iva=$_POST['cciv'];
    }else{
      $costo_sin_iva=$_POST['cosi'];
      $costo_con_iva=$costo_sin_iva*$iva;
    }
    $precio_venta=($_POST['porc']+1)*$costo_con_iva;
  }

  $obsoleto=$_POST['obs'];
  $piva=$_POST['piva'];
  $depi=$_POST['depi'];
  $frac=$_POST['frac'];
  if(!$_POST['obs'])$obsoleto=0;
  if(!$_POST['piva'])$piva=0;
  if(!$_POST['depi'])$depi=0;
  if(!$_POST['frac'])$frac=0;
  $sql = "update items set descripcion='".$_POST['desc']."', codigo_barras='".$_POST['codb']."', unidad='".$_POST['uni']."', costo_sin_iva=".$costo_sin_iva.", fecha_compra='".date("Y-m-d")."', marcado=".$_POST['porc'].", precio_venta=".$precio_venta.", ultima_modif_precio='".date("Y-m-d")."', stock=".$_POST['sto'].", costo_con_iva=".$costo_con_iva.", categoria='".$_POST['cat']."', codigo_proveedor='".$_POST['codp']."', obsoleto=".$obsoleto.", iva=".$iva.", pag_iva=".$piva.", depende_id=".$depi.", fraccion=".$frac." where item_id=".$_POST['itid'];
	echo $sql."<br><br>";   
	$result=mysql_query($sql,$conn) or die(mysql_error());
	$sql="insert into histo_cargas set item_id=".$_POST['itid'].", cantidad=".$_POST['sto'].", costo_sin_iva=".$costo_sin_iva.", marcado=".$_POST['porc'].", precio_venta=".$precio_venta.", fecha='".date("Y-m-d")."', hora='".date("H:i:s")."'";
	echo $sql."<br>";   
       	$result=mysql_query($sql,$conn) or die(mysql_error());

  
}









?>

