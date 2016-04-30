<?php
session_start();
echo "<meta http-equiv='Content-type' content='text/html;charset=utf-8' />";


$conn = mysql_connect("localhost","fedesuarez","ambrosio"); 
mysql_set_charset("utf8");

mysql_select_db("donbeto",$conn);

date_default_timezone_set("America/Argentina/Catamarca");

echo "<h3>Carga:</h3>";
echo "<table><tr><td>";
echo "<form action='carga.php' method='post'>";
echo "Código: <input type='text' name='venta' autofocus='autofocus' value=''>";
echo " <input type='radio' name='nuevo' value='1'> Nuevo ";
echo "<input type='hidden' name='flag_venta' value='1'>";
echo" <input type='submit' value='Agregar'>";
echo" </form></td>";
echo "</tr></table>";

if($_POST["limpiar"]==1){
unset($_SESSION['acumulado_ventas']);
}


if($_POST["flag_venta"]==1 || $_SESSION['acumulado_ventas'][0] || $_POST["nuevo"]){
  if($_POST["venta"]){
    $venta=$_POST["venta"];
    $sql = "select item_id from items where codigo_barras='$venta' or codigo_nuestro='$venta'";
    $result=mysql_query($sql,$conn) or die(mysql_error());
    $fila=mysql_fetch_row($result);
    if($_POST["flag_venta"]==1 && $fila[0]){
      $venta=$_POST["venta"];
      if(!$_SESSION['acumulado_ventas'][0]){
	$array[0]=$venta;
	$_SESSION['acumulado_ventas']=$array;
      }else{
	array_push($_SESSION['acumulado_ventas'],$venta);
      }
    }
  }

  if($_SESSION['acumulado_ventas'][0] && !$_POST['flag_cerrar'] || $_POST["nuevo"]){
    if($_POST['flag_borrar'])unset($_SESSION['acumulado_ventas'][$_POST['borrar_item']]);
    echo "<form action='carga.php' method='post'>";
    echo "<table border='1' style='width:60%'><tr><td align='center'>Carga</td><td align='center'>Item id</td><td align='center'>Descripción</td><td align='center'>Código barras</td><td align='center'>Código nuestro</td><td align='center'>Unidad</td><td align='center'>Costo sin IVA</td><td align='center'>IVA</td><td align='center'>paga IVA</td><td align='center'>Fecha compra</td><td align='center'>%</td><td align='center'>Precio venta</td><td align='center'>Condición mayorista</td><td align='center'>Descuento mayorista</td><td align='center'>Última modificación precio</td><td align='center'>Stock</td><td align='center'>Costo con IVA</td><td align='center'>Categoría</td><td align='center'>Código proveedor</td><td align='center'>Obs</td><td align='center'>Prov id</td><td align='center'>Porcentaje última modificación precio</td><td align='center'>Depende id</td><td align='center'>Fracción dependencia</td></tr>";

    if(!$_POST['nuevo']){      

      foreach($_SESSION['acumulado_ventas'] as $key=>$valor){
	$sql = "select * from items where codigo_barras='$valor' or codigo_nuestro='$valor'";
	$result=mysql_query($sql,$conn) or die(mysql_error());
	$fila = mysql_fetch_row($result);
	foreach($fila as $key2=>$valor2){
	  if($valor2==null)$fila[$key2]=0;
	}
	echo "<tr>
<td>$key</td>
<td>$fila[0]</td>
<td><input type='text' name='desc' value='$fila[1]' style='width: 80px;'</td>
<td><input type='text' name='codb' value='$fila[2]' style='width: 40px;'</td>
<td><input type='text' name='codn' value='$fila[3]' style='width: 60px;'</td>
<td><input type='text' name='uni' value='$fila[4]' style='width: 40px;'</td>
<td><input type='text' name='cosi' value='$fila[5]' style='width: 40px;'</td>
<td><input type='text' name='iva' value='$fila[19]' style='width: 40px;'</td>
<td><input type='text' name='piva' value='$fila[22]' style='width: 40px;'</td>
<td><input type='text' name='feco' value='$fila[6]' style='width: 65px;'</td>
<td><input type='text' name='porc' value='$fila[7]' style='width: 25px;'</td>
<td>$fila[8]</td>
<td><input type='text' name='conm' value='$fila[9]' style='width: 60px;'</td>
<td><input type='text' name='desm' value='$fila[10]' style='width: 40px;'</td>
<td><input type='text' name='ump' value='$fila[11]' style='width: 65px;'</td>
<td><input type='text' name='sto' value='$fila[12]' style='width: 40px;'</td>
<td>$fila[13]</td>
<td><input type='text' name='cat' value='$fila[14]' style='width: 60px;'</td>
<td><input type='text' name='codp' value='$fila[15]' style='width: 40px;'</td>
<td><input type='text' name='obs' value='$fila[16]' style='width: 20px;'</td>
<td><input type='text' name='prid' value='$fila[17]' style='width: 25px;'</td>
<td><input type='text' name='pump' value='$fila[18]' style='width: 40px;'</td>
<td><input type='text' name='depi' value='$fila[20]' style='width: 40px;'</td>
<td><input type='text' name='frac' value='$fila[21]' style='width: 40px;'</td>
</tr>"; 
      }
    }else{
	$sql = "select codigo_nuestro from items order by codigo_nuestro";
	$result=mysql_query($sql,$conn) or die(mysql_error());
	while ($fila = mysql_fetch_object($result)) {
	  $id_nuevo=$fila->codigo_nuestro;
	}
	$id_nuevo=$id_nuevo+1;
	echo "<tr>
<td></td>
<td></td>
<td><input type='text' name='desc' value='' style='width: 80px;'</td>
<td><input type='text' name='codb' value='' style='width: 40px;'</td>
<td><input type='text' name='codn' value='".$id_nuevo."' style='width: 60px;'</td>
<td><input type='text' name='uni' value='unidad' style='width: 40px;'</td>
<td><input type='text' name='cosi' value='0' style='width: 40px;'</td>
<td><input type='text' name='iva' value='1.21' style='width: 40px;'</td>
<td><input type='text' name='piva' value='1' style='width: 40px;'</td>
<td><input type='text' name='feco' value='".date("Y-m-d")."' style='width: 65px;'</td>
<td><input type='text' name='porc' value='1.3' style='width: 25px;'</td>
<td></td>
<td><input type='text' name='conm' value='' style='width: 60px;'</td>
<td><input type='text' name='desm' value='0' style='width: 40px;'</td>
<td><input type='text' name='ump' value='".date("Y-m-d")."' style='width: 65px;'</td>
<td><input type='text' name='sto' value='0' style='width: 40px;'</td>
<td><input type='text' name='coci' value='0' style='width: 40px;'</td>
<td><input type='text' name='cat' value='4' style='width: 60px;'</td>
<td><input type='text' name='codp' value='' style='width: 40px;'</td>
<td><input type='text' name='obs' value='0' style='width: 20px;'</td>
<td><input type='text' name='prid' value='18' style='width: 25px;'</td>
<td><input type='text' name='pump' value='0' style='width: 40px;'</td>
<td><input type='text' name='depi' value='0' style='width: 25px;'</td>
<td><input type='text' name='frac' value='0' style='width: 40px;'</td>

</tr>"; 

    echo "<input type='hidden' name='flag_nuevo' value='1'>";

    }
    
  

    echo "</table><br>";
    echo "<input type='hidden' name='flag_cerrar' value='1'>";
    echo "<br><br><input type='submit' value='Terminar carga' style='font-size: 2em'>";
    echo" </form>";

  
  }
}


/************************/
/*    CERRAR VENTA      *
/************************/

if($_POST['flag_cerrar']){
  if($_SESSION['acumulado_ventas'][0] || $_POST['flag_nuevo'] ){
    //    date_default_timezone_set("America/Argentina/Catamarca");

    if(!$_POST['flag_nuevo']){
      foreach($_SESSION['acumulado_ventas'] as $key=>$valor){
	$sql = "select item_id from items where codigo_barras='$valor' or codigo_nuestro='$valor'";
	$result=mysql_query($sql,$conn) or die(mysql_error());
	$fila = mysql_fetch_row($result);
	$sql = "update items set descripcion='".$_POST['desc']."', codigo_barras='".$_POST['codb']."', codigo_nuestro='".$_POST['codn']."', unidad='".$_POST['uni']."', costo_sin_iva=".$_POST['cosi'].", fecha_compra='".date("Y-m-d")."', marcado=".$_POST['porc'].", precio_venta=".($_POST['porc']+1)*$_POST['cosi']*$_POST['iva'].", condicion_mayorista='".$_POST['conm']."', descuento_mayorista=".$_POST['desm'].", ultima_modif_precio='".date("Y-m-d")."', stock=".$_POST['sto'].", costo_con_iva=".$_POST['cosi']*$_POST['iva'].", categoria='".$_POST['cat']."', codigo_proveedor='".$_POST['codp']."', obsoleto=".$_POST['obs'].", proveedor_id=".$_POST['prid'].", porcent_ult_mod_precio=".$_POST['pump'].", iva=".$_POST['iva'].", pag_iva=".$_POST['piva'].", depende_id=".$_POST['depi'].", fraccion=".$_POST['frac']." where item_id=$fila[0]";
	echo $sql."<br>";   
	$result=mysql_query($sql,$conn) or die(mysql_error());
	$sql="insert into histo_cargas set item_id=$fila[0], cantidad=".$_POST['sto'].", costo_sin_iva=".$_POST['cosi'].", marcado=".$_POST['porc'].", precio_venta=".($_POST['porc']+1)*$_POST['cosi']*$_POST['iva'].", fecha='".date("Y-m-d")."'";
	$result=mysql_query($sql,$conn) or die(mysql_error());
	
      }
    }else{

      if($_POST['cosi']){
	$sql = "insert into items set descripcion='".$_POST['desc']."', codigo_barras='".$_POST['codb']."', codigo_nuestro='".$_POST['codn']."', unidad='".$_POST['uni']."', costo_sin_iva=".$_POST['cosi'].", fecha_compra='".date("Y-m-d")."', marcado=".$_POST['porc'].", precio_venta=".($_POST['porc']+1)*$_POST['cosi']*$_POST['iva'].", condicion_mayorista='".$_POST['conm']."', descuento_mayorista=".$_POST['desm'].", ultima_modif_precio='".date("Y-m-d")."', stock=".$_POST['sto'].", costo_con_iva=".$_POST['cosi']*$_POST['iva'].", categoria='".$_POST['cat']."', codigo_proveedor='".$_POST['codp']."', obsoleto=".$_POST['obs'].", proveedor_id=".$_POST['prid'].", porcent_ult_mod_precio=".$_POST['pump'].", iva=".$_POST['iva'].", pag_iva=".$_POST['piva'].", depende_id=".$_POST['depi'].", fraccion=".$_POST['frac'];
      }else{
	$sql = "insert into items set descripcion='".$_POST['desc']."', codigo_barras='".$_POST['codb']."', codigo_nuestro='".$_POST['codn']."', unidad='".$_POST['uni']."', costo_sin_iva=".$_POST['coci']/$_POST['iva'].", fecha_compra='".date("Y-m-d")."', marcado=".$_POST['porc'].", precio_venta=".($_POST['porc']+1)*$_POST['coci'].", condicion_mayorista='".$_POST['conm']."', descuento_mayorista=".$_POST['desm'].", ultima_modif_precio='".date("Y-m-d")."', stock=".$_POST['sto'].", costo_con_iva=".$_POST['coci'].", categoria='".$_POST['cat']."', codigo_proveedor='".$_POST['codp']."', obsoleto=".$_POST['obs'].", proveedor_id=".$_POST['prid'].", porcent_ult_mod_precio=".$_POST['pump'].", iva=".$_POST['iva'].", pag_iva=".$_POST['piva'].", depende_id=".$_POST['depi'].", fraccion=".$_POST['frac'];

      }

      //echo $sql."<br>";   
      $result=mysql_query($sql,$conn) or die(mysql_error());

      $sql="select item_id from items where descripcion='".$_POST['desc']."' and codigo_nuestro='".$_POST['codn']."'";
      $result=mysql_query($sql,$conn) or die(mysql_error());
      $fila4=mysql_fetch_row($result);

      if($_POST['cosi']){
	$sql="insert into histo_cargas set item_id=$fila4[0], cantidad=".$_POST['sto'].", costo_sin_iva=".$_POST['cosi'].", marcado=".$_POST['porc'].", precio_venta=".($_POST['porc']+1)*$_POST['cosi']*$_POST['iva'].", fecha='".date("Y-m-d")."', hora='".date("H:i:s")."'";
      }else{
	$sql="insert into histo_cargas set item_id=$fila4[0], cantidad=".$_POST['sto'].", costo_sin_iva=".$_POST['coci']/$_POST['iva'].", marcado=".$_POST['porc'].", precio_venta=".($_POST['porc']+1)*$_POST['coci'].", fecha='".date("Y-m-d")."', hora='".date("H:i:s")."'";

      }
      $result=mysql_query($sql,$conn) or die(mysql_error());


    }




    unset($_SESSION['acumulado_ventas']);
    echo "<font size='6em' color='blue'>Carga terminada</font>";
    echo "<form action='carga.php' method='post'>";
    echo "<input type='submit' value='Nueva carga' style='font-size: 2em'>";
    echo" </form>";

  }else{
    echo "<font color='red'>ERROR: No hay valores cargados</font><br>";
  }

}



/******************/
/*    BORRAR      *
/******************/



echo "<h3>Modificar carga:</h3>";
echo"<table>";
echo"<tr><td>";
echo "<form action='carga.php' method='post'>";
echo "<input type='number' name='borrar_item' value=''>";
echo "<input type='hidden' name='flag_borrar' value='1'>";
echo" <input type='submit' value='Borrar ítem'>";
echo" </form></td>";
echo"<td>";
echo "<form action='carga.php' method='post'>";
echo "<input type='hidden' name='limpiar' value='1'>";
echo" <input type='submit' value='Limpiar todo'>";
echo" </form></td></tr></table>";



/******************/
/*    BUSQUEDA    *
/******************/


echo "<h3>Búsqueda:</h3>";
echo "<form action='carga.php' method='post'>";
echo "<input type='text' name='name' value=''>";
echo "<input type='hidden' name='flag_busqueda' value='1'>";
echo" <input type='submit' value='Buscar'>";
echo" </form></body></html>";

if($_POST["name"]){
  $busqueda=$_POST["name"];
  $sql = "select item_id, codigo_barras, codigo_nuestro, descripcion, precio_venta, stock from items where codigo_barras='$busqueda' or codigo_nuestro='$busqueda' or descripcion like '%$busqueda%'";
  $result=mysql_query($sql,$conn) or die(mysql_error());
  echo "<h4>Búsqueda:</h4>";
  echo "<table border='1' style='width:60%'><tr><td align='center'>Item id</td><td align='center'>Código de barras</td><td align='center'>Código DonBeto</td><td align='center'>Descripción</td><td align='center'>Precio de venta</td><td align='center'>Stock</td></tr>";
  while ($fila = mysql_fetch_object($result)) {
    echo "<tr><td>$fila->item_id</td><td>$fila->codigo_barras</td><td>$fila->codigo_nuestro</td><td>$fila->descripcion</td><td align='center'>\$$fila->precio_venta</td><td align='center'>$fila->stock</td></tr>";
  }
  echo "</table>";


}else{
}




?>



