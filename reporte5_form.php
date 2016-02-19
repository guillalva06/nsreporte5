<?php
  //require_once("{$CFG->libdir}/formslib.php");
  require_once("$CFG->libdir/formslib.php");

 
  class reporte5_form extends moodleform {
 
    function definition() {
 
        $mform =& $this->_form;
        $mform->addElement('text','filtro1', 'Palabra clave');
        $mform->addRule('filtro1', null, 'required', null, 'client');
        $mform->setType('filtro1', PARAM_TEXT);                

        $periodos= array('0'=>'Seleccione un periodo');
        $select=$mform->addElement('select', 'periodos', 'Periodo',$periodos);
        $mform->addElement('hidden','periodo');
        $mform->setType('periodo', PARAM_INT);        

        $variantes = array('0'=>'Seleccione una variante');
        $mform->addElement('select','variantes','Variante',$variantes);
        $mform->addElement('hidden','variante');
        $mform->setType('variante', PARAM_INT);
        $this->add_action_buttons();
      
  }
}