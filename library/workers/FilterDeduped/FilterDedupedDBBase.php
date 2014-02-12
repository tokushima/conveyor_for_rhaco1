<?php
Rhaco::import("tag.feed.Rss20");
/**
 * FilterDedupedDBBase
 * @author Takuya Sato
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */

class FilterDedupedDBBase {
	
	function setItem($item)
	{
		return false;
	}
	
	function isDeduped($item)
	{
		return false;
	}
	
	function parseDSN($dsn)
	{
		preg_match('/(\w+):\/\/(([^@:]+)(:([^@]+))?)?@([^:\/]+)(:([0-9]+))?\/(\w+)/', $dsn, $matches);
		
		$ret = '';
		$ret = $this->_addparam('name', $matches[9], $ret);
		$ret = $this->_addparam('user', $matches[3], $ret);
		$ret = $this->_addparam('password', $matches[5], $ret);
		$ret = $this->_addparam('type', $matches[1], $ret);
		$ret = $this->_addparam('host', $matches[6], $ret);
		$ret = $this->_addparam('port', $matches[8], $ret);
		
		return $ret;
	}
	
	function _addparam($name, $value, $text)
	{
		if (!empty($text)) {
			$text .= ',';
		}
		if (!empty($value)) {
			$text .= sprintf('%s=%s',$name, $value);
		}
		
		return $text;
	}
	
}
