<?php
class block_nsreporte5 extends block_base {

    public function init() {
        $this->title = get_string('blocktitle', 'block_nsreporte5');
    }

    public function get_content(){

      if ($this->content !== null){
        return $this->content;
      }
      $this->content =  new stdClass;
      $html = '';      
      global $COURSE;
      $context = context_course::instance($COURSE->id);
      $urlreporte = new moodle_url('/blocks/nsreporte5/reporte5.php');      
      $html .=html_writer::link($urlreporte,'reporte');
      $this->content->text   = $html;   
      if (! empty($this->config->text)) {
        $this->content->text = $this->config->text;
      }
      return $this->content;

    }

    public function instance_allow_multiple() {
      return false;
    }

    public function applicable_formats() {
      return array(
        'course-view' => true,
        'site'=>false,
        'my'=>false);
    }
}