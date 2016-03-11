<?php
	require_once('../../config.php');	
	require_once('DataBase.php');	
	global $DB;
	$typefunction = $_GET['id'];
	switch ($typefunction) {
		case 0:
			$periodos = DataBase::getPeriodos();
			echo json_encode($periodos, JSON_UNESCAPED_UNICODE);	
			break;
		case 1:
			$parent = $_GET['parent'];
			$variantes = DataBase::getVariantes($parent);
			echo json_encode($variantes, JSON_UNESCAPED_UNICODE);	
			break;
		default:
			# code...
			$path = $_GET['path'];
			$namecategory = $_GET['namecategory'];
			$componentes = DataBase::getComponentes($namecategory,$path);
			echo json_encode($componentes, JSON_UNESCAPED_UNICODE);
			break;
	}		

