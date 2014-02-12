<?php
Rhaco::import("resources.Message");
Rhaco::import("database.model.TableObjectBase");
Rhaco::import("database.model.DbConnection");
class UrlsTable extends TableObjectBase{
	var $id;  
	var $url;  
	var $pubdate;  
	function UrlsTable(
		$id=null
	){
		$this->__init__($id);
	}
	function __init__(
		$id=null
	){
		$this->id = null;
		$this->url = null;
		$this->pubdate = null;
		$this->setId($id);
	}
	function table(){
		return new Table(Rhaco::constant("DATABASE_deduped_PREFIX")."urls","urls","Urls","model.Urls");
	}
	function setId($value){
		$this->id = is_null($value) ? null : intval(sprintf("%d",StringUtil::convertZenhan($value)));
	}
	function getId(){
		return $this->id;
	}

	function columnId(){
		return new Column(Urls::table(),"column='id',variable='id',type='serial',size=22,require=false,primary=true,chartype='',label='".Message::_("id")."'");
	}
	function setUrl($value){
		$this->url = $value;
	}
	function getUrl(){
		return $this->url;
	}

	function columnUrl(){
		return new Column(Urls::table(),"column='url',variable='url',type='string',size=null,require=true,primary=false,chartype='',label='".Message::_("url")."'");
	}
	function setPubdate($value){
		$this->pubdate = $value;
	}
	function getPubdate(){
		return $this->pubdate;
	}

	function columnPubdate(){
		return new Column(Urls::table(),"column='pubdate',variable='pubdate',type='string',size=null,require=false,primary=false,chartype='',label='".Message::_("pubdate")."'");
	}
	function verifyObject(){
		Rhaco::import("workers.FilterDeduped.DBSQLite.model.verify.UrlsVerify");	
		return new UrlsVerify();
	}
	function verify(){
		Rhaco::import("workers.FilterDeduped.DBSQLite.model.verify.UrlsVerify");
		$verify = new UrlsVerify();
		return $verify->verify($this);
	}
	function primaryKey(){
		return array(Urls::columnId(),);
	}
	function toString(){
		return implode("_",array($this->getId(),));
	}

	function connection(){
		return new DbConnection("deduped");
	}
}
?>