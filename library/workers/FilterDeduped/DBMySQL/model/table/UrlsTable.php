<?php
Rhaco::import("resources.Message");
Rhaco::import("database.model.TableObjectBase");
Rhaco::import("database.model.DbConnection");

/**
 * 
 */
class UrlsTable extends TableObjectBase{
	/**  */
	var $id;
	/**  */
	var $url;
	/**  */
	var $pubdate;

	function UrlsTable($id=null){
		$this->__init__($id);
	}
	function __init__($id=null){
		$this->id = null;
		$this->url = null;
		$this->pubdate = null;
		$this->setId($id);
	}
	function connection(){
		return new DbConnection("deduped");
	}
	function table(){
		return new Table(Rhaco::constant("DATABASE_deduped_PREFIX")."urls","Urls");
	}
	/**
	 * 
	 * @return database.model.Column
	 */
	function columnId(){
		$column = new Column("column='id',variable='id',type='serial',size=22,require=false,primary=true,unique=false,chartype='',reference='',requireWith='',uniqueWith='',label='".Message::_("id")."'");
		return $column;
	}
	/**  */
	function setId($value){
		$this->id = TableObjectUtil::cast($value,"serial");
	}
	/**  */
	function getId(){
		return $this->id;
	}
	/**
	 * 
	 * @return database.model.Column
	 */
	function columnUrl(){
		$column = new Column("column='url',variable='url',type='string',size=null,require=true,primary=false,unique=false,chartype='',reference='',requireWith='',uniqueWith='',label='".Message::_("url")."'");
		return $column;
	}
	/**  */
	function setUrl($value){
		$this->url = TableObjectUtil::cast($value,"string");
	}
	/**  */
	function getUrl(){
		return $this->url;
	}
	/**
	 * 
	 * @return database.model.Column
	 */
	function columnPubdate(){
		$column = new Column("column='pubdate',variable='pubdate',type='string',size=null,require=false,primary=false,unique=false,chartype='',reference='',requireWith='',uniqueWith='',label='".Message::_("pubdate")."'");
		return $column;
	}
	/**  */
	function setPubdate($value){
		$this->pubdate = TableObjectUtil::cast($value,"string");
	}
	/**  */
	function getPubdate(){
		return $this->pubdate;
	}


}
?>