<?php
Rhaco::import("workers.FilterDeduped.FilterDedupedDBBase");
Rhaco::import("io.FileUtil");
Rhaco::import("database.model.DbConnection");
Rhaco::import("database.DbUtilSQLite");
Rhaco::import("workers.FilterDeduped.DBSQLite.model.Urls");

/**
 * DBSQLite
 * @author Takuya Sato
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */

class DBSQLite extends FilterDedupedDBBase {
	
	var $dbUtil;
	
	function DBSQLite($dsn) {
		$connection = new DBConnection(array(
				"","","","SQLite",$dsn,"","",true
			));
		$this->dbUtil = new DbUtil($connection);
		$this->dbUtil->query("CREATE TABLE urls(id INTEGER PRIMARY KEY, url, pubdate)");
		$this->dbUtil->commit();
	}
	
	function setItem($item) {
		$criteria = new Criteria(
			Q::eq(Urls::columnUrl(), $item->getLink()),
			Q::eq(Urls::columnPubdate(), $item->getPubdate())
		);
		$url = $this->dbUtil->get(new Urls(), $criteria);
		if (!$url) {
			$url = new Urls();
		}
		
		$url->setUrl($item->getLink());
		$url->setPubdate($item->getPubDate());
		$url->save($this->dbUtil);
		
		return false;
	}
	
	function isDeduped($item) {
		$criteria = new Criteria(
			Q::eq(Urls::columnUrl(), $item->getLink()),
			Q::eq(Urls::columnPubdate(), $item->getPubdate())
		);
		$url = $this->dbUtil->get(new Urls(), $criteria);
		if (!$url) {
			return false;
		}
		
		return true;
	}
	
}
