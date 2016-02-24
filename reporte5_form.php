<?php
  //require_once("{$CFG->libdir}/formslib.php");
  require_once("$CFG->libdir/formslib.php");

 
  class reporte5_form extends moodleform {
 
    function definition() {
 
        $mform =& $this->_form;

        $periodos= array('0'=>'Seleccione un periodo');
        $select=$mform->addElement('select', 'periodos', 'Periodo',$periodos);               

        $variantes = array('0'=>'Seleccione una variante');
        $mform->addElement('select','variantes','Variante',$variantes);
        
        $mform->addElement('text','nombre_categoria', 'Palabra clave');
        $mform->addRule('nombre_categoria', null, 'required', null, 'client');
        $mform->setType('nombre_categoria', PARAM_TEXT);   

        $componentes = array('0'=>'Seleccione un Componente');
        $select=$mform->addElement('select', 'componentes', 'Componente',$componentes);
        $mform->addElement('hidden','componente_id');
        $mform->setType('componente_id', PARAM_INT);   

        //Disable form checker when reload 
        $mform->disable_form_change_checker();     
      
  }
}