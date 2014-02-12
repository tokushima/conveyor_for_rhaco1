<?php
Rhaco::import(Conveyaml::importpath("FilterDeduped.FilterDedupedDBBase"));
Rhaco::import("io.FileUtil");
Rhaco::import("database.model.DbConnection");
Rhaco::import("database.DbUtil");
Rhaco::import(Conveyaml::importpath("FilterDeduped.DBMySQL.model.Urls"));
Rhaco::import("lang.Variable");

/**
 * DBSQLite
 * @author Takuya Sato
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */

class DBMySQL extends FilterDedupedDBBase {
	
	var $dbUtil;
	
	function DBMySQL($dsn) {
		
		$params = $this->parseDSN($dsn);
		
		$connection = new DBConnection($params);
		$this->dbUtil = new DbUtil($connection);
		$this->dbUtil->query("CREATE TABLE IF NOT EXISTS urls(id INTEGER PRIMARY KEY auto_increment, url VARCHAR(255), pubdate DATETIME)");
		$this->dbUtil->commit();
	}
	
	function setItem($item) {
		$criteria = new Criteria(
			Q::eq(Urls::columnUrl(), $item->getLink()),
			Q::eq(Urls::columnPubdate(), date('Y-m-d H:i:s', strtotime($item->getPubdate()) ) )
		);
		$url = $this->dbUtil->get(new Urls(), $criteria);
		if (!$url) {
			$url = new Urls();
		}
		
		$url->setUrl($item->getLink());
		$url->setPubdate( date('Y-m-d H:i:s', strtotime($item->getPubdate()) ) );
		$url->save($this->dbUtil);
		
		return false;
	}
	
	function isDeduped($item) {
		$criteria = new Criteria(
			Q::eq(Urls::columnUrl(), $item->getLink()),
			Q::eq(Urls::columnPubdate(), date('Y-m-d H:i:s', strtotime($item->getPubdate()) ) )
		);
		$url = $this->dbUtil->get(new Urls(), $criteria);
		if (!$url) {
			return false;
		}
		
		return true;
	}
	
}
