<?php
Rhaco::import("model.FilterBase");
Rhaco::import("tag.feed.Rss20");
Rhaco::import("io.FileUtil");
/**
 * FilterDeduped
 * @author Takuya Sato
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class FilterDeduped extends FilterBase{
	
	function execute($rss20)
	{
		$engine = $this->variable("engine");
		$ret = Rhaco::import(Conveyaml::importpath("FilterDeduped.".$engine.".".$engine));
		$db = new $engine($this->variable("dsn"));
		
		$channel = $rss20->getChannel();
		$items = $rss20->getItem();
		
		$rss20_filtered = new Rss20();
		$rss20_filtered->setChannel($channel->getTitle(),
			$channel->getDescription(),
			$channel->getLink(),
			"ja"
		);
		
		foreach($items as $item) {
			if (!$db->isDeduped($item)) {
				$db->setItem($item);
				$rss20_filtered->setItem($item);
			}
		}
		
		return $rss20_filtered;
	}
	
	function description() {
		return "以前に読み込まれているアイテムを削除する";
	}
	
	function config(){
		$basedir = FileUtil::parseFilename(__FILE__);
		$basedir = substr($basedir, 0, strrpos($basedir, "/"));
		$dirs = FileUtil::dirs($basedir, false, false);
		
		$params = array();
		foreach($dirs as $dir) {
			if (substr($dir, 0, 2) == 'DB') {
				$params[$dir] = $dir;
			}
		}
		
		return array(
			'engine' => array('ストレージエンジン', 'select', $params),
			'dsn' => 'DSN',
		);
	}

	function rhacover(){
		return "1.4.1";
	}
	
}
?>
