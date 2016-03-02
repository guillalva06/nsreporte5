<link rel="stylesheet" href="//cdn.jsdelivr.net/pure/0.5.0/pure-min.css">
<?php
  require_once('../../config.php'); 
  require_once('reporte5_form.php');    
  require_once('Teacher.php');
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
      if($componente_id!=0){        
        //Find courses that are contained on the component 
        if($componente_id!=-1){
          $set_courses = $DB->get_records_sql("SELECT  distinct({$CFG->prefix}course.id), 
            {$CFG->prefix}course.fullname as namecourse,
            {$CFG->prefix}course_categories.path as coursepath 
            from  {$CFG->prefix}course, {$CFG->prefix}nspermission,
                  {$CFG->prefix}course_categories                                
            where  {$CFG->prefix}course.category = ?
            and {$CFG->prefix}course.id = {$CFG->prefix}nspermission.courseid",array($componente_id));          
        }else{
          //Find all the courses that have permission in all the components on the period-variant
          $path = '/'.$periodo_id.'/'.$variante_id.'/%';          
          $set_courses = $DB->get_records_sql("SELECT  distinct({$CFG->prefix}course.id), 
            {$CFG->prefix}course.fullname as namecourse, 
            {$CFG->prefix}course_categories.path as coursepath
            from  {$CFG->prefix}course, {$CFG->prefix}nspermission, 
                  {$CFG->prefix}course_categories
            where  {$CFG->prefix}course.id = {$CFG->prefix}nspermission.courseid
            and {$CFG->prefix}course.category ={$CFG->prefix}course_categories.id
            and {$CFG->prefix}course_categories.path like ?",array($path));          
        }        
        $courses = array();        
        $paths = array();
        foreach ($set_courses as $courseid => $course) {
          $courses[$courseid]=array();    
          $paths[$courseid]=$course->coursepath;             
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
              $courses[$courseid][$data->idnumber] = new Teacher($data->firstname,$data->lastname,$courseid,
                $course->namecourse);              
            }
            array_push($courses[$courseid][$data->idnumber]->permissions ,  date("Y-m-d H:i:s",$data->timecreated));
          }
        }        
          //Print Table of Permissions
          $attr = array('border' => 0, 'width' => '100%',
                        'class' => 'pure-table');        
          $oddrow = array('border' => 0, 'width' => '100%',
                        'class' => 'pure-table-odd');             
          //Rows of the Table
          foreach ($courses as $key=>$course) {
            //Informaticon of every course               
            $countrows = 0;   
          //Generar el path
          $strpath = '';          
          $arraypath = explode('/',$paths[$key]);
          for($i = 1; $i < count($arraypath);$i++){
            $nameparent = $DB->get_record_sql('SELECT mdl_course_categories.name 
              from mdl_course_categories 
              where mdl_course_categories.id = ?',array($arraypath[$i]));
            $strpath.=$nameparent->name;     
            if($i!=count($arraypath)-1){
              $strpath.='/';     
            }
          }
      echo html_writer::tag('h3',$strpath);                         
          echo html_writer::start_tag('table',$attr); 
          //Headers of the table
          echo html_writer::start_tag('tr');                   
            echo html_writer::tag('th','ID Curso');
            echo html_writer::tag('th','Nombre del Curso');
            echo html_writer::tag('th','Docente');
            echo html_writer::tag('th','Cédula');
            echo html_writer::tag('th','Datos de Autorización');
          echo html_writer::end_tag('tr');                          
            foreach ($course as $teacherid => $teacher) {         
              $countrows++;     
              if($countrows%2!=0){
                echo html_writer::start_tag('tr',$oddrow);  
              }else{
                 echo html_writer::start_tag('tr');  
              }              
              # Information of each teacher              
              echo html_writer::tag('td',$teacher->courseid);              
              echo html_writer::tag('td',$teacher->coursename);
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
            echo html_writer::end_tag('table');  
            echo html_writer::tag('br',null);
          }                      
      }            
  	}
  }   
  echo $OUTPUT->footer();

	

