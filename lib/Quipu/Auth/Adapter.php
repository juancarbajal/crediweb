<?php
class Quipu_Auth_Adapter implements Zend_Auth_Adapter_Interface{
  private $_tablename=null;
  private $_identityColumn=null;
  private $_credentialColumn=null;
  private $_identity;
  private $_credential=null;
  private $_resultRow=null;
  public function __construct( $tableName=null, $identityColumn=null, $credentialColumn=null) {
    $this->_tableName=$tableName;
    $this->_identityColumn=$identityColumn;
    $this->_credentialColumn=$credentialColumn;
  } //end function
  public function setIdentity($identity) {
    $this->_identity=$identity;
    return $this;
  } //end function
  public function setCredential($credential) {
    $this->_credential=$credential;
    return $this;    
  } //end function
  public function setIdentityColumn($identityColumn) {
    $this->_identityColumn=$identityColumn;
    return $this;
  } //end function
  public function setCredentialColumn($credentialColumn) {
    $this->_credentialColumn=$credentialColumn;
    return $this;
  } //end function  
  function getResultRowObject() {
    if (!$this->_resultRow) return false;
    $returnObject = new stdClass();
    foreach ($this->_resultRow as $resultColumn => $resultValue) {
      $returnObject->{$resultColumn} = $resultValue;
    }
    return $returnObject;
  } //end function
  
  public function authenticate() {
    $authResult = array(
                        'code'     => Zend_Auth_Result::FAILURE,
                        'identity' => $this->_identity,
                        'messages' => array()
                        );
    $usr=Doctrine_Query::create()->from($this->_tableName)->where($this->_identityColumn.'=?',$this->_identity)->fetchArray();    
    if (count($usr)==0) {
      $authResult['code'] = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
      $authResult['messages'][] = 'Login failed';
    } //end if
    else{
      if ($usr[0][$this->_credentialColumn]==$this->_credential){
        $this->_resultRow=$usr[0];
        $authResult['code'] = Zend_Auth_Result::SUCCESS;
        $authResult['messages'][] = 'Login succesful';
      }
      else{
        $authResult['code'] = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
        $authResult['messages'][] = 'Password failed';
      }
    }
    return new Zend_Auth_Result($authResult['code'],$authResult['identity'],$authResult['messages']);
  } //end function  
  
}//end class

?>