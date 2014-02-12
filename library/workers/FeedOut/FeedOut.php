<?php
Rhaco::import("model.PublishBase");
/**
 * FeedOut
 * @author TOKUSHIMA Kazutaka
 */
class FeedOut extends PublishBase{
	function execute($rss){
		$rss->output();
		return $rss;
	}
	function description(){
		return "Feedを出力";
	}
}
?>