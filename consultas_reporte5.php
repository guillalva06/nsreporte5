<?php
	require_once('../../config.php');	
	global $DB;
	$typefunction = $_GET['id'];
	switch ($typefunction) {
		case 0:
			$periodos = $DB->get_records_sql("SELECT {$CFG->prefix}course_categories.id,
				{$CFG->prefix}course_categories.name 
				from {$CFG->prefix}course_categories
				where {$CFG->prefix}course_categories.parent = 0");	
			echo json_encode($periodos, JSON_UNESCAPED_UNICODE);	
			break;
		case 1:
			$parent = $_GET['parent'];
			$variantes = $DB->get_records_sql("SELECT {$CFG->prefix}course_categories.id,
				{$CFG->prefix}course_categories.name 
				from {$CFG->prefix}course_categories
				where {$CFG->prefix}course_categories.parent = ?",array($parent));
			echo json_encode($variantes, JSON_UNESCAPED_UNICODE);	
			break;
		default:
			# code...
			$path = $_GET['path'];
			$namecategory = $_GET['namecategory'];
			$componentes = $DB->get_records_sql('SELECT * from mdl_course_categories as course_categories 
				where course_categories.coursecount != 0 
				and (select count(mdl_course.id) 
					from mdl_course 
					where mdl_course.category = course_categories.id 
					and mdl_course.shortname like "%PA%" )!=0 
				and course_categories.name like ? 
				and course_categories.path like ?', array($namecategory,$path));
			echo json_encode($componentes, JSON_UNESCAPED_UNICODE);
			break;
	}		

