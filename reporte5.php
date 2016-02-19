<?php
  require_once('../../config.php'); 
  require_once('reporte5_form.php');  
  GLOBAL $DB, $COURSE, $CFG, $OUTPUT;
  //print_object($CFG->prefix);

  $id = optional_param('id', 0, PARAM_INT);
  //$selected = optional_param('selected', '', PARAM_TEXT);
  //$contextid = required_param('context_id',PARAM_INT);
  require_login();
  $PAGE->set_context(context_system::instance());
  //$PAGE->set_context(context::instance_by_id($contextid));
  $PAGE->set_title('Reporte 5');
  $PAGE->set_url('/blocks/nsreporte5/reporte5.php');
  $PAGE->requires->jquery();
  $urljs = new moodle_url('/blocks/nsreporte5/amd/src/reporte5.js');
  $PAGE->requires->js($urljs);
  $form = new reporte5_form();
  echo $OUTPUT->header();
  $url = new moodle_url('/course/view.php', array('id' => 0));  
  if($form->is_cancelled()){
    redirect($url);
  }else{
  	if ($form->get_data()){

  	}else{
  		$form->display();	
  	}
  }   
  echo $OUTPUT->footer();

	

