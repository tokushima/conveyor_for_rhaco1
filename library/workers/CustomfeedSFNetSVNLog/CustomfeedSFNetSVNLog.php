<?php
Rhaco::import("model.SubscriptionBase");
Rhaco::import("lang.DateUtil");
Rhaco::import("lang.StringUtil");
Rhaco::import("network.http.Browser");
Rhaco::import("tag.model.SimpleTag");
Rhaco::import("tag.feed.Rss20");
/**
 * CustomFeedSFNetSVNLog
 * @author Takuya Sato
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class CustomfeedSFNetSVNLog extends SubscriptionBase{
	function execute($variable){
		$project_id = $this->variable("project_id");
		$base_url = "http://".$project_id.".svn.sourceforge.net/";
		$title = $project_id."'s SourceForge.net SVN Log";
		
		$rss20 = new Rss20();
		$rss20->setChannel($title,
			$title,
			$base_url,
			"ja"
		);
		$source = new RssSource($base_url, $title);
		
		$revision = 0;
		
		$browser = new Browser();
		$page = $browser->get($base_url."viewvc/".$project_id."/");
		$tag = new SimpleTag();
		$tag->set($page);
		foreach($tag->getIn("a") as $aTag) {
			if (preg_match("/^\/viewvc\/rhaco\?view=rev&amp;revision=([0-9]+)/", $aTag->getParameter("href"), $matches)) {
				$revision = intval($matches[1]);
			}
		}
		
		$min_revision = $revision - 5;
		if ($min_revision < 1) { $min_revision = 1; }
		for($i = $revision;$i > $min_revision;$i --) {
			$link = $base_url."viewvc/".$project_id."?view=rev&revision=".$i;
			$page = $browser->get($link);
			$tag = new SimpleTag();
			$tag->set($page);
			$tableTag = $tag->getIn("table", false, 1, 1);
			$item = new RssItem20();
			$item->setTitle("Revision ".$i);
			$item->setLink($link);
			foreach($tableTag[0]->getIn("tr") as $trTag) {
				switch ($trTag->getInValue("th")) {
				case "Author:":
					break;
				case "Date:":
					$item->setPubDate($trTag->getInValue("td"));
					break;
				case "Log Message:":
					$item->setDescription($trTag->getInValue("pre"));
					break;
				}
			}
			$rss20->setItem($item);
		}
		$rss20 = $this->merge($variable,$rss20);
		return $rss20;
	}
	function description(){
		return "SourceForge.netのSVNリポジトリの最近のコミットログを取得します。";
	}
	function config(){
		return array(
				"project_id"=>"SF.netに登録されているプロジェクト名",
				);
	}
}


