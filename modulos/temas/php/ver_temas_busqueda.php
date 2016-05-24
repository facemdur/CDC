<?php
require_once "../../../php/check.php";
include "../../../lib/link_mysql.php";
require_once "../../../lib/template.inc";
require_once "../../../php/funciones_comunes.php";
session_start();
/*Recibo los parametros para cargar el formulario ABM
Si son vacios es porque tengo que agregar uno nuevo.
*/
$t = new Template('../templates/');
//Archivos comunes
$t->set_file(array(
	"ver"				=> "ver_temas_busqueda.html",
	"un"				=> "un_temas.html",
	"una_opcion"		=> "una_opcion.html",
));


//recuperar_perfiles_con_modulo($id_tablamodulo,$link_mysql);
$titulo="Listado de temas";
$t->set_var("titulo",$titulo);
//
//Otras Funciones
//$t->set_var("funcion_pdf","modulos/pasajeros/php/printpdf.php");
$t->set_var("funcion_excel","modulos/temas/php/exportar_excel.php?tipo=xls");
$t->set_var("funcion_doc","modulos/temas/php/exportar_excel.php?tipo=doc");
$t->set_var("funcion_pdf","modulos/temas/php/exportar_excel.php");
//




$url="'modulos/temas/php/ver_temas.php'";
$id="'listado_temas'";
$vars="'es_buscar=si&id_tablamodulo=$id_tablamodulo&";	
$vars.="tabla09_nombre='+ver_busqueda_temas.tabla09_nombre.value";	
$t->set_var("funcion_busqueda","cargar_post($url,$id,$vars)");

// New Paginador
$totalporpag=10;
if(!$offset){ 
	$off=0;$offset=1;
}
else{
    $off=($offset-1);
}
$ini=$off*$totalporpag;
// End New	
$sql="select * from tabla_09_temas 
		order by tabla09_nombre ASC  
		Limit $totalporpag OFFSET $ini ";
//echo $sql;
$rs = $pdo->query($sql);//
$num_rows = $rs->rowCount();
if ($num_rows>0)
{
	while ($row = $rs->fetch())
	{
		$id_tabla09=$row["id_tabla09"];
		$t->set_var("tabla09_nombre",htmlentities($row["tabla09_nombre"],ENT_QUOTES));
		$t->set_var("tabla09_descripcion",htmlentities($row["tabla09_descripcion"],ENT_QUOTES));
		$t->set_var("tabla09_subtema",htmlentities($row["tabla09_subtema"],ENT_QUOTES));
		

		$url="'modulos/temas/php/ver_temas_abm.php'";
		$id="'tabs-$id_tablamodulo'";
		$vars="'offset=$offset&id_tablamodulo=$id_tablamodulo&id_tabla09=$id_tabla09'";
		$t->set_var("funcion_editar","cargar_post($url,$id,$vars)");	
		
		$url="'modulos/temas/php/abm_temas_interfaz.php'";
		$vars="'nombre_funcion=borrar_temas&";
		$vars.="id_tabla09=$id_tabla09'";
		$url_exito="'modulos/temas/php/ver_temas_busqueda.php'";
		$id="'tabs-$id_tablamodulo'";
		$vars_exito="'offset=$offset&id_tablamodulo=$id_tablamodulo'";
		$msg="'Esta seguro que quiere eliminar el Registro?'";
		$t->set_var("funcion_borrar","eliminar_mostrar($url,$vars,$url_exito,$id,$vars_exito,$msg);");
		
		$t->parse("LISTADO","un",true);
	}
}	
else
{
	$t->set_var("LISTADO","<tr align='center' class='alt'><td colspan='5'>No se encuentran Registros Cargados. </td></tr>");
	
}	
	// New Paginador
	$qrT="select * from tabla_09_temas " ;
	$rs = $pdo->query($qrT);//
	$totalregistros = $rs->rowCount();
	$t->set_var("cantidad",$totalregistros);
	$totalpaginas=$totalregistros/$totalporpag;
	$test=split("\.",$totalpaginas);
	$pag=''; 
	if(isset($test[1]))
	{
		$totalpaginas=$test[0]+1;
	}
	// << Anterior
	if($offset>1)
	{
		$pag.="<td><a href=\"javascript:cargar_post('modulos/temas/php/ver_temas.php','listado_temas','offset=$off&id_tablamodulo=$id_tablamodulo');\"><< Anterior</a> | </td>";
	}
	else
	{
		$pag.="<td></td>";
	}
						 
	// Numeros
	if($totalpaginas>15)
	{
		$faltan=($totalpaginas-$off);
		if($faltan<15)
		{
			$ter=$totalpaginas;
			$com=$off -(15-($totalpaginas-$off));
		}
		elseif($off<8)
		{
			$ter=15;
			$com=1;
		}
		else
		{
			$ter=$off+7;
			$com=$off-7;
		}
	}
	else
	{
		$com=1;
		$ter=$totalpaginas;
	}
		  
	$pag.="<td align='center'>";
	for($i=$com;$i<=$ter;$i++)
	{
		if($i==$offset)
		{
			$pag.="<font color=#000000><b>$i</b></font>&nbsp;";
		}
		else
		{
			$pag.="<a href=\"javascript:cargar_post('modulos/temas/php/ver_temas.php','listado_temas','offset=$i&id_tablamodulo=$id_tablamodulo');\">$i</a>&nbsp;";
		}				 	 
	}
	$pag.="</td>";
					// Siguiente >>
	if($offset<$totalpaginas)
	{
		$ofs=$offset+1;
		$pag.="<td> | <a href=\"javascript:cargar_post('modulos/temas/php/ver_temas.php','listado_temas','offset=$ofs&id_tablamodulo=$id_tablamodulo');\">Siguiente >></a></td>";
	}else{
		$pag.="<td></td>";
	}
	$t->set_var("paginas","<table align=center><tr>".$pag."</tr></table>");
		//End Paginador
	
	$url="'modulos/temas/php/ver_temas_abm.php'";
	$id="'tabs-$id_tablamodulo'";
	$vars="'offset=$offset&id_tablamodulo=$id_tablamodulo'";
	$t->set_var("funcion_agregar","cargar_post($url,$id,$vars);");
	$t->set_var("icono_agregar","icon-plus.gif");	

$t->pparse("OUT", "ver");
?>