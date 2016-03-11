<link rel="stylesheet" href="//cdn.jsdelivr.net/pure/0.5.0/pure-min.css">
<?php
  require_once('../../config.php'); 
  require_once('reporte5_form.php');    
  require_once('Teacher.php');
  require_once('DataBase.php');
  GLOBAL $DB, $COURSE, $CFG, $OUTPUT;  
    
  $componente_id = optional_param('componente_id', 0, PARAM_INT);  
  $variante_id = optional_param('variante_id',0, PARAM_INT);  
  $periodo_id = optional_param('periodo_id', 0, PARAM_INT);  
  require_login();
  $PAGE->set_context(context_system::instance());  
  $PAGE->set_title('Reporte 5');
  $PAGE->set_url('/blocks/nsreporte5/reporte5.php');
  $PAGE->requires->jquery();
  $PAGE->requires->jquery_plugin('ui');
  $PAGE->requires->jquery_plugin('ui-css');  
  $urljs = new moodle_url('/blocks/nsreporte5/amd/src/reporte5.js');
  $PAGE->requires->js($urljs);
  $form = new reporte5_form();
  echo $OUTPUT->header();
  $url = new moodle_url('/course/view.php', array('id' => 0));  
  if($form->is_cancelled()){
    //Form cancelled
    redirect($url);
  }else{
  	if ($form->get_data()){
      //Read data from the form
      $fromform=$form->get_data();
      if($componente_id==0){
        //Case when the user doesn't pick a component on the form
        $componente_id=-1;
      }
      $reporteurl = new moodle_url('/blocks/nsreporte5/reporte5.php', array('componente_id'=>$componente_id,
        'periodo_id'=>$periodo_id,'variante_id'=>$variante_id));
      redirect($reporteurl);
  	}else{
      //Original form without Data
  		$site = get_site();
      $form->display();
      $path = '/'.$periodo_id.'/'.$variante_id.'/%';     	      
      if($componente_id!=0){        
        //Find courses that are contained on the component 
        $courses = DataBase::getDataReport($componente_id,$path);       
          //Print Table of Permissions
        DataBase::printDataReport($courses);
      }            
  	}
  }   
  echo $OUTPUT->footer();

	

