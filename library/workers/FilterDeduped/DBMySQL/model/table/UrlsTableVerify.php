<?php
Rhaco::import("lang.Validate");
Rhaco::import("lang.StringUtil");
Rhaco::import("exception.ExceptionTrigger");
Rhaco::import("exception.model.MaxLengthException");
Rhaco::import("exception.model.MinLengthException");
Rhaco::import("exception.model.RequireException");
Rhaco::import("exception.model.DataTypeException");
Rhaco::import("resources.Message");

class UrlsTableVerify{
	var $valid = true;

	function UrlsTableVerify(){
	}
	function verify(&$tableObject){
		foreach(get_class_methods($this) as $methodName){
			if(preg_match("/^verify(.+)$/i",$methodName)) $this->$methodName($tableObject);
		}
		return $this->valid;	
	}
	function verifyId(&$tableObject){
		$value = $tableObject->getId();
		if(!empty($value) && !Validate::isIntegerLength($value,22)){
			ExceptionTrigger::raise(new DataTypeException(array($this->namedId())),$this->_validName("id"));
		}
	}
	function verifyUrl(&$tableObject){
		$value = $tableObject->getUrl();
		if($value === "" || $value === null){
			ExceptionTrigger::raise(new RequireException(array($this->namedUrl())),$this->_validName("url"));
		}
	}
	function verifyPubdate(&$tableObject){
		$value = $tableObject->getPubdate();
	}

	function namedId(){
		return Message::_("id");
	}
	function namedUrl(){
		return Message::_("url");
	}
	function namedPubdate(){
		return Message::_("pubdate");
	}
	function _validName($name){
		$this->valid = false;
		return "Urls_".$name;
	}
}

?>