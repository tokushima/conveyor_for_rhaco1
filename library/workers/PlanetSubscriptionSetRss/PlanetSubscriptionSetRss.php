<?php
Rhaco::import("model.SubscriptionBase");
/**
 * PlanetSubscriptionSetRss
 * @author SHIGETA Takeshiro
 * 

 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
class PlanetSubscriptionSetRss extends SubscriptionBase{
	function execute($variable){
		return Rhaco::getVariable("planetsubscriptionsetrss__rss");
	}
	function description(){
		return "内部使用プラグイン";
	}
}
?>