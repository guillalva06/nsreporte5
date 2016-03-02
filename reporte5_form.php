<?php
  //require_once("{$CFG->libdir}/formslib.php");
  require_once("$CFG->libdir/formslib.php");

 
  class reporte5_form extends moodleform {
 
    function definition() {
 
        $mform =& $this->_form;

        //Choose Period
        $periodos= array('0'=>'Seleccione un periodo');
        $select=$mform->addElement('select', 'periodos', 'Periodo',$periodos);  
        $mform->addRule('periodos', null, 'required', null, 'client');             
        //Hidden value to save the selection
        $mform->addElement('hidden','periodo_id');
        $mform->setType('periodo_id', PARAM_INT);           

        //Select variant
        $variantes = array('0'=>'Seleccione una variante');
        $mform->addElement('select','variantes','Variante',$variantes);
        $mform->addRule('variantes', null, 'required', null, 'client');
        //Hidden value to save the selection
        $mform->addElement('hidden','variante_id');
        $mform->setType('variante_id', PARAM_INT);           
        
        //Text field for the component's name
        $mform->addElement('text','nombre_categoria', 'Palabra clave');
        //$mform->addRule('nombre_categoria', null, 'required', null, 'client');
        $mform->setType('nombre_categoria', PARAM_TEXT);   

        //Select component
        $componentes = array('0'=>'Seleccione un componente');
        $select=$mform->addElement('select', 'componentes', 'Componente',$componentes);
        $mform->addElement('hidden','componente_id');
        $mform->setType('componente_id', PARAM_INT);   

        //Disable form checker when reload 
        $mform->disable_form_change_checker();     
        //Action Button 
        $this->add_action_buttons();
      
  }
}