<?php


class DataBase{


	public static function getPeriodos(){
		global $DB,$CFG;
		$periodos = $DB->get_records_sql("SELECT {$CFG->prefix}course_categories.id,
						{$CFG->prefix}course_categories.name 
						from {$CFG->prefix}course_categories
						where {$CFG->prefix}course_categories.parent = 0");	
		return $periodos;
	}

	public static function getVariantes($parent){
		global $DB,$CFG;
		$variantes = $DB->get_records_sql("SELECT {$CFG->prefix}course_categories.id,
			{$CFG->prefix}course_categories.name 
			from {$CFG->prefix}course_categories
			where {$CFG->prefix}course_categories.parent = ?",array($parent));
		return $variantes;
	}


	public static function getComponentes($namecategory, $path){
		global $DB,$CFG;
		$componentes = $DB->get_records_sql('SELECT * from mdl_course_categories as course_categories 
			where course_categories.coursecount != 0 
			and (select count(mdl_course.id) 
				from mdl_course 
				where mdl_course.category = course_categories.id 
				and mdl_course.shortname like "%PA%" )!=0 
			and course_categories.name like ? 
			and course_categories.path like ?', array($namecategory,$path));		
		return $componentes;
	}

	private static function getCoursesOnComponent($componente_id){
		global $DB,$CFG;
		$set_courses = $DB->get_records_sql("SELECT  distinct({$CFG->prefix}course.id), 
            {$CFG->prefix}course.fullname as namecourse,
            {$CFG->prefix}course_categories.path as coursepath 
            from  {$CFG->prefix}course, {$CFG->prefix}nspermission,
                  {$CFG->prefix}course_categories                                
            where  {$CFG->prefix}course.category = ?
            and {$CFG->prefix}course.id = {$CFG->prefix}nspermission.courseid",array($componente_id));
		return $set_courses;
	}

	private static function getCoursesOnPath($path){
		global $DB,$CFG;
	    $set_courses = $DB->get_records_sql("SELECT  distinct({$CFG->prefix}course.id), 
	        {$CFG->prefix}course.fullname as namecourse, 
	        {$CFG->prefix}course_categories.path as coursepath
	        from  {$CFG->prefix}course, {$CFG->prefix}nspermission, 
	              {$CFG->prefix}course_categories
	        where  {$CFG->prefix}course.id = {$CFG->prefix}nspermission.courseid
	        and {$CFG->prefix}course.category ={$CFG->prefix}course_categories.id
	        and {$CFG->prefix}course_categories.path like ?",array($path));  
	    return $set_courses;
	}

	private static function getPermissionsOnCourse($courseid){
		global $DB,$CFG;        
        $logs = $DB->get_records_sql("SELECT {$CFG->prefix}nspermission.id, 
              {$CFG->prefix}user.idnumber,{$CFG->prefix}user.firstname, 
              {$CFG->prefix}user.lastname,{$CFG->prefix}nspermission.timecreated, 
              {$CFG->prefix}nspermission.idnumberquiz as quiz
              from {$CFG->prefix}nspermission, {$CFG->prefix}course,
              {$CFG->prefix}user
              where {$CFG->prefix}nspermission.userid = {$CFG->prefix}user.id
              and {$CFG->prefix}course.id = {$CFG->prefix}nspermission.courseid
              and {$CFG->prefix}course.id = ?",array($courseid));
        return $logs;
	}


	public static function getDataReport($componente_id,$path){      
        //Find courses that are contained on the component 
        if($componente_id!=-1){
          $set_courses = DataBase::getCoursesOnComponent($componente_id);          
        }else{
          //Find all the courses that have permission in all the components on the period-variant               
          $set_courses = DataBase::getCoursesOnPath($path);          
        }        
        $courses = array();        
        $paths = array();
        foreach ($set_courses as $courseid => $course) {
          $courses[$courseid]=array();    
          $paths[$courseid]=$course->coursepath;             
          //Find teachers and their permissions
          $logs = DataBase::getPermissionsOnCourse($courseid);
          foreach ($logs as $permissionsid => $data) {                        
            if(!array_key_exists($data->idnumber, $courses[$courseid])){
              $courses[$courseid][$data->idnumber] = new Teacher($data->firstname,$data->lastname,$courseid,
                $course->namecourse);              
            }
            array_push($courses[$courseid][$data->idnumber]->permissions ,  date("Y-m-d H:i:s",$data->timecreated));
          }
        }        
		return $courses;
	}

	public static function printDataReport($courses){
      $attr = array('border' => 0, 'width' => '100%',
          'class' => 'pure-table');        
      $oddrow = array('border' => 0, 'width' => '100%',
          'class' => 'pure-table-odd');             
      //Rows of the Table
      $htmltable = '';
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
        $htmltable.= html_writer::tag('h3',$strpath);                         
        $htmltable.= html_writer::start_tag('table',$attr); 
        //Headers of the table
        $htmltable.= html_writer::start_tag('tr');                   
        $htmltable.= html_writer::tag('th','ID Curso');
        $htmltable.= html_writer::tag('th','Nombre del Curso');
        $htmltable.= html_writer::tag('th','Docente');
        $htmltable.= html_writer::tag('th','Cédula');
        $htmltable.= html_writer::tag('th','Datos de Autorización');
        $htmltable.= html_writer::end_tag('tr');                          
        foreach ($course as $teacherid => $teacher) {         
          $countrows++;     
          if($countrows%2!=0){
            $htmltable.= html_writer::start_tag('tr',$oddrow);  
          }else{
            $htmltable.= html_writer::start_tag('tr');  
          }              
          # Information of each teacher              
          $htmltable.= html_writer::tag('td',$teacher->courseid);              
          $htmltable.= html_writer::tag('td',$teacher->coursename);
          $htmltable.= html_writer::tag('td',$teacher->firstname.' '.$teacher->lastname);              
          $htmltable.= html_writer::tag('td',$teacherid);
          $htmltable.= html_writer::start_tag('td');
          $htmltable.= html_writer::start_tag('table',$attr);                
          if($countrows%2!=0){
            $htmltable.= html_writer::start_tag('tr',$oddrow);  
          }else{
            $htmltable.= html_writer::start_tag('tr');  
          }  
          $htmltable.= html_writer::tag('th','Fecha');                  
          $htmltable.= html_writer::end_tag('tr');                
          foreach ($teacher->permissions as $permission) {
              # code...                
              if($countrows%2!=0){
                $htmltable.= html_writer::start_tag('tr',$oddrow);                    
              }else{                  
                $htmltable.= html_writer::start_tag('tr');                                                 
              }                
              $htmltable.= html_writer::tag('td',$permission);                
              $htmltable.= html_writer::end_tag('tr');
          }
          $htmltable.= html_writer::end_tag('table'); 
          $htmltable.= html_writer::end_tag('td');              
          $htmltable.= html_writer::end_tag('tr');
        }            
        $htmltable.= html_writer::end_tag('table');  
        $htmltable.= html_writer::tag('br',null);
      } 	
    return $htmltable;	
	}
	

}