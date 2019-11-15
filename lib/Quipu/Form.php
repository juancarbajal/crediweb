<?php
class Quipu_Form extends Zend_Form{
  public $formDecorators = array(
                                 'FormElements',
                                 //array('HtmlTag', array('tag'=>'div')),
                                 array('Fieldset',array('class'=>'q_form')),
                                 'Form',
                                 //array('Errors', array('placement'=>'prepend')),
                                 );
  function init() {
    $this->setDisableLoadDefaultDecorators(true); 
    $this->setDecorators($this->formDecorators);
  } //end function
  /*  function render() {
    
  } //end function
  */
  
  
} //end class

?>