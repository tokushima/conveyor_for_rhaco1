<?php
Rhaco::import("model.SubscriptionBase");
Rhaco::import("lang.DateUtil");
Rhaco::import("lang.StringUtil");
Rhaco::import("network.http.Browser");
Rhaco::import("tag.model.SimpleTag");
Rhaco::import("tag.feed.Rss20");
/**
 * CustomfeedLingr
 * @author Takuya Sato
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class CustomfeedLingr extends SubscriptionBase{
	function execute($variable){
		$room_id = $this->variable("room_id");
		
		$base_url = "http://www.lingr.com/room/".$room_id;
		
		$browser = new Browser();
		$page = $browser->get($base_url."/archives");
		$tag = new SimpleTag();
		$tag->set($page);
		$title = $tag->getInValue("title");
		
		$rss20 = new Rss20();
		$rss20->setChannel($title,
			$title,
			$base_url,
			"ja"
		);
		
		$logs = array();
		
		$name = "";
		$description = "";
		$date = "";
		$link = "";
		foreach($tag->getIn("ul") as $ulTag) {
			if ($ulTag->param("id") == "messages") {
				foreach($ulTag->getIn("li") as $liTag) {
					$id = $liTag->param("id");
					if (substr($id, 0, 7) == "handle-") {
						$date_temp = $liTag->f("span[0].value()");
						if (!empty($date_temp)) {
							$date = $this->parseDate($date_temp);
						}
						$name = $liTag->f("span[1].value()");
					} else if (substr($id, 0, 4) == "msg-") {
						$link = $liTag->f("a[0].param('href')");
						$description = $liTag->getInValue("span");
					}
					
					if ( (!empty($name)) && (!empty($description)) ) {
						$log = array();
						$log['author'] = $name;
						$log['date'] = $date;
						$log['description'] = $description;
						$log['link'] = $link;
						$logs[] = $log;
						
						$name = "";
						$description = "";
						$link = "";
					}
				}
				
				break;
			}
		}
		
		$logs = array_reverse($logs);
		foreach($logs as $log) {
			$item = new RssItem20();
			
			$item->setAuthor($log['author']);
			$item->setPubDate($log['date']);
			$item->setDescription($log['description']);
			$item->setTitle($log['author']);
			$item->setLink($log['link']);
			
			$rss20->setItem($item);
		}
		
		$rss20 = $this->merge($variable,$rss20);
		return $rss20;
	}
	function description(){
		return "Lingrの最近のログを取得します。";
	}
	function config(){
		return array(
				"room_id"=>"部屋ID（[http://lingr.com/room/xxxxx]の[xxxxx]の部分）",
				);
	}
	
	// 今のところ日本専用
	function parseDate($date) {
		$year = intval(date('Y'));
		$month = 0;
		$day   = 0;
		if (preg_match('/([0-9]+):([0-9]+)([ap]m)/', $date, $matches)) {
			$hour = intval($matches[1]);
			$min = intval($matches[2]);
			$sec = 0;
			if ($matches[3] == 'pm') {
				$hour = $hour + 12;
			}
			if (preg_match('/\(([A-Za-z]+) ([0-9]+)(, ([0-9]+))?\)/', $date, $matches)) {
				$month = intval(date("n", strtotime($matches[1].' 01')));
				$day = intval($matches[2]);
			} else {
				$month = intval(date("n"));
				$day = intval(date("j"));
			}
			
			return sprintf('%04d-%02d-%02dT%02d:%02d:%02d+9:00', $year, $month, $day, $hour, $min, $sec);
		}
		
		return '';
	}
}


