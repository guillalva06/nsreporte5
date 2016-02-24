<link rel="stylesheet" href="//cdn.jsdelivr.net/pure/0.5.0/pure-min.css">
<?php
  require_once('../../config.php'); 
  require_once('reporte5_form.php');    
  require_once('Teacher.php');
  GLOBAL $DB, $COURSE, $CFG, $OUTPUT;  
    
  $componente_id = optional_param('componente_id', 0, PARAM_TEXT);  
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
      $reporteurl = new moodle_url('/blocks/nsreporte5/reporte5.php', array('componente_id'=>$componente_id));
      redirect($reporteurl);
  	}else{
      //Original form without Data
  		$site = get_site();
      $form->display();	      
      if($componente_id!=0){
        //echo('componente_id:'.$componente_id.'.');
        //Find courses that are contained on the component 
        $set_courses = $DB->get_records_sql("SELECT  distinct({$CFG->prefix}course.id), 
          {$CFG->prefix}course.fullname as namecourse 
          from  {$CFG->prefix}course, {$CFG->prefix}nspermission 
          where  {$CFG->prefix}course.category = ?
          and {$CFG->prefix}course.id = {$CFG->prefix}nspermission.courseid",array($componente_id));        
        $courses = array();        
        foreach ($set_courses as $courseid => $course) {
          $courses[$courseid]=array();      
          //$courses[$courseid]['namecourse']=$course->namecourse;
          //Find teachers and their permissions
          $logs = $DB->get_records_sql("SELECT {$CFG->prefix}nspermission.id, 
              {$CFG->prefix}user.idnumber,{$CFG->prefix}user.firstname, 
              {$CFG->prefix}user.lastname,{$CFG->prefix}nspermission.timecreated, 
              {$CFG->prefix}nspermission.idnumberquiz as quiz
              from {$CFG->prefix}nspermission, {$CFG->prefix}course,
              {$CFG->prefix}user
              where {$CFG->prefix}nspermission.userid = {$CFG->prefix}user.id
              and {$CFG->prefix}course.id = {$CFG->prefix}nspermission.courseid
              and {$CFG->prefix}course.id = ?",array($courseid));
          foreach ($logs as $permissionsid => $data) {                        
            if(!array_key_exists($data->idnumber, $courses[$courseid])){
              $courses[$courseid][$data->idnumber] = new Teacher($data->firstname,$data->lastname);              
            }
            array_push($courses[$courseid][$data->idnumber]->permissions ,  date("Y-m-d H:i:s",$data->timecreated));
          }
        }
        //print_object($courses);
          //Print Table of Permissions
          $attr = array('border' => 0, 'width' => '100%',
                        'class' => 'pure-table');        
          $oddrow = array('border' => 0, 'width' => '100%',
                        'class' => 'pure-table-odd');             
          echo html_writer::start_tag('table',$attr); 
          //Headers of the table
          echo html_writer::start_tag('tr');                   
            echo html_writer::tag('th','ID Curso');
            echo html_writer::tag('th','Docente');
            echo html_writer::tag('th','Cédula');
            echo html_writer::tag('th','Datos de Autorización');
          echo html_writer::end_tag('tr');
          //Rows of the Table
          foreach ($courses as $course) {
            //Informaticon of every course     
            $countrows = 0;                   
            foreach ($course as $teacherid => $teacher) {         
              $countrows++;     
              if($countrows%2!=0){
                echo html_writer::start_tag('tr',$oddrow);  
              }else{
                 echo html_writer::start_tag('tr');  
              }              
              # Information of each teacher
              echo html_writer::tag('td','ID Curso');              
              echo html_writer::tag('td',$teacher->firstname.' '.$teacher->lastname);              
              echo html_writer::tag('td',$teacherid);
              echo html_writer::start_tag('td');
                echo html_writer::start_tag('table',$attr);                
                if($countrows%2!=0){
                  echo html_writer::start_tag('tr',$oddrow);  
                }else{
                   echo html_writer::start_tag('tr');  
                }  
                  echo html_writer::tag('th','Fecha');                  
                echo html_writer::end_tag('tr');                
              foreach ($teacher->permissions as $permission) {
                # code...                
                if($countrows%2!=0){
                  echo html_writer::start_tag('tr',$oddrow);                    
                }else{                  
                  echo html_writer::start_tag('tr');                                                 
                }                
                echo html_writer::tag('td',$permission);                
                echo html_writer::end_tag('tr');
              }
                echo html_writer::end_tag('table'); 
              echo html_writer::end_tag('td');              
              echo html_writer::end_tag('tr');
            }            
          }
          echo html_writer::end_tag('table');                
      }            
  	}
  }   
  echo $OUTPUT->footer();

	

