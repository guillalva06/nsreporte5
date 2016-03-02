<?php
  class Teacher{

    public $firstname;
    public $lastname;
    public $permissions;
    public $courseid;
    public $coursename;

    function __construct($firstname,$lastname,$courseid,$coursename){
      $this->firstname = $firstname;
      $this->lastname = $lastname;
      $this->permissions = array();
      $this->courseid = $courseid;
      $this->coursename = $coursename;
    }

    public function displayname(){
      echo ($this->firstname." ".
        $this->lastname);
    }

    public function getName(){
      return $this->firstname." ".$this.lastname;
    }



  }
