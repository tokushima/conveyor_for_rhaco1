<?php
Rhaco::import('model.PublishBase');
Rhaco::import('Services.Lingr');// PEAR

/**
 * NotifyLingr
 *
 * @author  riaf <riafweb@gmail.com>
 * @package Conveyor
 * @version $Id: NotifyLingr.php 330 2007-10-25 11:16:46Z riafweb $
 */
class NotifyLingr extends PublishBase
{
    function execute($rss){
        $lingr = new Services_Lingr($this->variable('api_key'));
        $session_id = $lingr->session->create();
        $options = array('nickname' => $this->variable('nickname'));
        $lingr->room->enter($session_id, $this->variable('room_id'), $options);

        $channel = $rss->getChannel();
        $items   = $rss->getItem();
        $lingr->room->say(sprintf('Title: %s', $channel->getTitle()));
        $lingr->room->say(sprintf('Description: %s', $channel->getDescription()));
        foreach($items as $item){
            $lingr->room->say(sprintf("Title: %s\nLink: %s", $item->getTitle(), $item->getLink()));
        }
        $lingr->room->leave();
        $lingr->session->destroy($session_id);
        return $rss;
    }

    function description(){
        return 'notifies updates to Lingr';
    }

    function config(){
        return array(
            'api_key' => 'Lingr API KEY',
            'room_id' => 'Room ID where to notify',
            'nickname' => 'nickname',
        );
    }

    function required(){
        return array(
            'Services.Lingr' => 'http://d.hatena.ne.jp/p4life/20070127/1169915026',
        );
    }
}

