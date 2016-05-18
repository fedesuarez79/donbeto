<?php
session_start();
echo "<meta http-equiv='Content-type' content='text/html;charset=utf-8' />";


$conn = mysql_connect("localhost","fedesuarez","ambrosio"); 
mysql_set_charset("utf8");

mysql_select_db("donbeto",$conn);


echo "<h3>Venta:</h3>";
echo "<table><tr><td>";
echo "<form action='index.php' method='post'>";
echo "Cantidad: <input type='number' name='cantidad' value='1' style='width: 40px;' min='1' step='any'>";
echo " Código: <input type='text' name='venta' autofocus='autofocus' value=''>";
echo "<input type='hidden' name='flag_venta' value='1'>";
echo" <input type='submit' value='Agregar'>";
echo" </form></td>";
echo "</tr></table>";

if($_POST["limpiar"]==1){
unset($_SESSION['acumulado_ventas']);
unset($_SESSION['acumulado_cantidades']);
}


if($_POST["flag_venta"]==1 || $_SESSION['acumulado_ventas'][0]){
  if($_POST["venta"]){
    $venta=$_POST["venta"];
    $sql = "select item_id from items where codigo_barras like '$venta%' or codigo_nuestro='$venta'";
    //echo $sql;
    $result=mysql_query($sql,$conn) or die(mysql_error());
    $fila=mysql_fetch_row($result);
    if($_POST["flag_venta"]==1 && $fila[0]){
      $venta=$_POST["venta"];
      $cantidad=$_POST["cantidad"];
      if(!$_SESSION['acumulado_ventas'][0]){
	$array[0]=$venta;
	$array2[0]=$cantidad;
	$_SESSION['acumulado_ventas']=$array;
	$_SESSION['acumulado_cantidades']=$array2;
      }else{
	array_push($_SESSION['acumulado_ventas'],$venta);
	array_push($_SESSION['acumulado_cantidades'],$cantidad);
      }
    }
  }
  if($_SESSION['acumulado_ventas'][0] && !$_POST['flag_cerrar']){
    if($_POST['flag_borrar']){
      unset($_SESSION['acumulado_ventas'][$_POST['borrar_item']]);
      unset($_SESSION['acumulado_cantidades'][$_POST['borrar_item']]);
    }
    $total_venta=0;
    echo "<table border='1' style='width:60%'><tr><td align='center'>Item</td><td align='center'>Cantidad</td><td align='center'>Descripción</td><td align='center'>Sub-total</td></tr>";
    foreach($_SESSION['acumulado_ventas'] as $key=>$valor){
      $cantidad2=$_SESSION['acumulado_cantidades'][$key];
      $sql = "select codigo_barras, codigo_nuestro, descripcion, precio_venta, depende_id, fraccion, marcado from items where codigo_barras like '$valor%' or codigo_nuestro='$valor'";
      $result=mysql_query($sql,$conn) or die(mysql_error());
      $fila = mysql_fetch_row($result);
      if(!$fila[4]){
	echo "<tr><td align='center'>$key</td><td align='center'>$cantidad2</td><td>$fila[2]</td><td align='center'>\$".round($fila[3]*$cantidad2,2)."</td></tr>"; 
	$total_venta=round($total_venta+($fila[3]*$cantidad2),2);
      }else{

	$sql = "select descripcion, costo_sin_iva, iva, marcado from items where item_id=".$fila[4];
	$result3=mysql_query($sql,$conn) or die(mysql_error());
	$fila3 = mysql_fetch_row($result3);
	echo "<tr><td align='center'>$key</td><td align='center'>$cantidad2</td><td>$fila[2]</td><td align='center'>\$".round($fila3[1]*$fila3[2]*(1+$fila[6])/$fila[5],2)*$cantidad2."</td></tr>"; 
	$total_venta=$total_venta+(round($fila3[1]*$fila3[2]*(1+$fila[6])/$fila[5],2)*$cantidad2);

      }
 
    }
  

    echo "<tr><td></td><td></td><td align='right'><font size=6>Total: </font></td><td align='center'><font size=6>".round($total_venta,2)."</font></td></tr>";
    echo "</table><br>";
    echo "<form action='index.php' method='post'>";
    echo "<input type='hidden' name='flag_cerrar' value='1'>";
    echo "<input type='radio' name='fiado' value='0' checked>Contado ";
    echo "<input type='radio' name='fiado' value='1'>Fiado ";

    $sql = "select cliente_id, nombre from clientes";
    $result=mysql_query($sql,$conn) or die(mysql_error());
    echo "<select name='cliente_id'>";
    echo "<option value='null'>Cliente</option>";
    while($fila = mysql_fetch_object($result)){
      if($fila->cliente_id)echo "<option value='$fila->cliente_id'>$fila->nombre</option>";
    }
    echo "</select>";

    echo "<br><br><input type='submit' value='Terminar venta' style='font-size: 2em'>";
    echo" </form>";

  
  }
}


/************************/
/*    CERRAR VENTA      *
/************************/

if($_POST['flag_cerrar']){
  if($_SESSION['acumulado_ventas'][0]){
    date_default_timezone_set("America/Argentina/Catamarca");
    foreach($_SESSION['acumulado_ventas'] as $key=>$valor){
      $cantidad2=$_SESSION['acumulado_cantidades'][$key];
      $sql = "select item_id, precio_venta, descripcion, depende_id, fraccion from items where codigo_barras like '$valor%' or codigo_nuestro='$valor'";
      $result=mysql_query($sql,$conn) or die(mysql_error());
      $fila = mysql_fetch_row($result);
      $sql = "insert into ventas (item_id, valor_venta, fecha, cantidad, fiado, cliente_id, hora) values($fila[0],".$cantidad2*$fila[1].",'".date("Y-m-d")."',$cantidad2, ".$_POST['fiado'].", ".$_POST['cliente_id'].", '".date("H:i:s")."')";
      //echo $sql."<br>";   
      $result=mysql_query($sql,$conn) or die(mysql_error());
      if(!$fila[3]){
	$sql = "update items set stock=stock-$cantidad2 where item_id=$fila[0]";
	$result=mysql_query($sql,$conn) or die(mysql_error());
	//echo $sql."<br>";
      }else{
	$sql = "update items set stock=stock-".round($cantidad2/$fila[4],2)." where item_id=$fila[3]";
	$result=mysql_query($sql,$conn) or die(mysql_error());
	//echo $sql."<br>";

      }
    }
    unset($_SESSION['acumulado_ventas']);
    unset($_SESSION['acumulado_cantidades']);
    echo "<font size='6em' color='blue'>Venta terminada</font>";
    echo "<form action='index.php' method='post'>";
    echo "<input type='submit' value='Nueva venta' style='font-size: 2em'>";
    echo" </form>";

  }else{
    echo "<font color='red'>ERROR: No hay valores cargados</font><br>";
  }

}





/******************/
/*    BUSQUEDA    *
/******************/


echo "<h3>Búsqueda:</h3>";
echo "<form action='index.php' method='post'>";
echo "<input type='text' name='name' value=''>";
echo "<input type='hidden' name='flag_busqueda' value='1'>";
echo" <input type='submit' value='Buscar'>";
echo" </form></body></html>";

if($_POST["name"]){
  $busqueda=$_POST["name"];

  $sql = "select codigo_barras, codigo_nuestro, descripcion, precio_venta, depende_id, fraccion, marcado from items where codigo_barras like '$busqueda%' or codigo_nuestro='$busqueda' or descripcion like '%$busqueda%'";
  $result=mysql_query($sql,$conn) or die(mysql_error());
  echo "<h4>Búsqueda:</h4>";
  echo "<table border='1' style='width:60%'><tr><td align='center'>Código de barras</td><td align='center'>Código DonBeto</td><td align='center'>Descripción</td><td align='center'>Precio de venta</td></tr>";
  //echo "<tr><td>$fila->codigo_barras</td><td>$fila->codigo_nuestro</td><td>$fila->descripcion</td><td align='center'>\$$fila->precio_venta</td></tr>";

  while ($fila = mysql_fetch_row($result)) {
      if(!$fila[4]){
	if($fila[3]>50){
	  $precio_venta=round($fila[3],0);
	}else if($fila[3]>1){
	  $precio_venta=round($fila[3],1);
	}else{
	  $precio_venta=round($fila[3],4);
	}
      }else{
	$sql = "select descripcion, costo_sin_iva, iva, marcado from items where item_id=".$fila[4];
	$result3=mysql_query($sql,$conn) or die(mysql_error());
	$fila3 = mysql_fetch_row($result3);
	$precio_venta=$fila3[1]*$fila3[2]*(1+$fila[6])/$fila[5]; 
	if($precio_venta>50){
	  $precio_venta=round($precio_venta,0);
	}else if($precio_venta>1){
	  $precio_venta=round($precio_venta,1);
	}else{
	  $precio_venta=round($precio_venta,4);
	}
	
      }
      echo "<tr><td align='center'>$fila[0]</td><td align='center'>$fila[1]</td><td>$fila[2]</td><td align='center'>\$".$precio_venta."</td></tr>"; 

  }
  echo "</table>";


}else{
}



/******************/
/*    BORRAR      *
/******************/



echo "<h3>Modificar venta:</h3>";
echo"<table>";
echo"<tr><td>";
echo "<form action='index.php' method='post'>";
echo "<input type='number' name='borrar_item' value=''>";
echo "<input type='hidden' name='flag_borrar' value='1'>";
echo" <input type='submit' value='Borrar ítem'>";
echo" </form></td>";
echo"<td>";
echo "<form action='index.php' method='post'>";
echo "<input type='hidden' name='limpiar' value='1'>";
echo" <input type='submit' value='Limpiar todo'>";
echo" </form></td></tr></table>";


?>
