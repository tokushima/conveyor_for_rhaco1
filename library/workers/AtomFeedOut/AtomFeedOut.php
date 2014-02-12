<?php
Rhaco::import("model.PublishBase");
Rhaco::import("tag.feed.Atom10");

class AtomFeedOut extends PublishBase{
	function execute($rss){		
		$feed		= new Atom10();
		$channel	= $rss->getChannel();
		$feed->setTitle($channel->getTitle());
		$feed->setSubtitle($channel->getDescription());
		$feed->setLink($channel->getLink());

		foreach($rss->getItem() as $item){
			$feed->setEntry($item->getTitle(),$item->getDescription(),$item->getLink());
		}
		$feed->output();
		return $rss;
	}
	function description(){
		return "Atom Feedを出力";
	}
}
?>