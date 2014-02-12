<?php
Rhaco::import('model.PublishBase');
Rhaco::import('tag.HtmlParser');

/**
 * PublishTakahashi
 *
 * @author  riaf <riafweb@gmail.com>
 * @package Conveyor
 * @version $Id$
 */
class PublishTakahashi extends PublishBase
{
    function PublishTakahashi(){
        $this->__init__(__FILE__);
    }

    function execute($rss){
        $parser = new HtmlParser();
        $channel = $rss->getChannel();
        $parser->setVariable('title', $channel->getTitle());
        $parser->setVariable('channel', $channel);
        $parser->setVariable('description', $channel->getDescription());
        $parser->setVariable('items', $rss->getItem());
        $parser->write($this->template('output.html', __FILE__));

        return $rss;
    }

    function description(){
        return '高橋メソッドで出力する';
    }
}

