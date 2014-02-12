<?php
Rhaco::import("model.WorkerBase");
Rhaco::import("lang.ArrayUtil");
/**
 * @author kazutaka tokushima
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class SubscriptionBase extends WorkerBase{
	/**
	 * merge two rss object.
	 *
	 * @param rss20 $rss1
	 * @param rss20 $rss2
	 * @return rss20
	 */
	function merge($rss1,$rss2){
		if($this->verify($rss1) && $this->verify($rss2) && sizeof($rss1->getItem()) > 0){
			foreach(ArrayUtil::arrays($rss2->getItem()) as $item){
				$rss1->setItem($item);
			}
			return $rss1;
		}
		return $rss2;
	}

}
?>