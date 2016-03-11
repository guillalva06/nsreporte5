<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php

	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=reporte5.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
	
	require_once('../../config.php');
	require_once('DataBase.php');	
	require_once('Teacher.php');
	$componente_id = $_GET['componente_id'];
	$path = '/'.$_GET['periodo_id'].'/'.$_GET['variante_id'].'/%';
	$data = DataBase::getDataReport($componente_id,$path);
	$view = DataBase::printDataReport($data,true);
	echo ($view);

