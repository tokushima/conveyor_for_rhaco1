<?php
Rhaco::import('model.PublishBase');
Rhaco::import('arbo.network.services.TwitterAPI');

/**
 * NotifyTwitter
 *
 * @author  Takuya Sato
 * @package Conveyor
 * @version $Id: NotifyTwitter.php 330 2007-10-25 11:16:46Z takuya0219 $
 */
class NotifyTwitter extends PublishBase
{
	function execute($rss){
		$twitter = new TwitterAPI($this->variable('login'), $this->variable('password'));
		
		$channel = $rss->getChannel();
		$items   = $rss->getItem();
		$items = array_reverse($items);
		foreach($items as $item){
			$body = '';
			switch($this->variable('body', 'title')) {
			case 'title':
				$body = $item->getTitle();
				break;
			case 'description':
				$body = $item->getDescription();
				break;
			}
			$twitter->status_update(sprintf("%s %s", $body, $item->getLink()));
		}
		return $rss;
	}
	
	function description(){
		return 'notifies updates to Twitter';
	}
	
	function config(){
		return array(
			'login' => 'Twitter ID',
			'password' => 'password',
			'body' => array('output text', 'select', array('title'=>'title', 'description'=>'description')),
		);
	}
	
	function required(){
		return array(
			'network.services.TwitterAPI' => 'arbo',
		);
	}
}

