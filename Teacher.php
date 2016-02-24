<?php
  class Teacher{

    public $firstname;
    public $lastname;
    public $permissions;

    function __construct($firstname,$lastname){
      $this->firstname = $firstname;
      $this->lastname = $lastname;
      $this->permissions = array();
    }

    public function displayname(){
      echo ($this->firstname." ".
        $this->lastname);
    }

    public function getName(){
      return $this->firstname." ".$this.lastname;
    }



  }
