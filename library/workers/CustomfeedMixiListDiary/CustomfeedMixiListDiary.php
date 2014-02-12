<?php
Rhaco::import('model.SubscriptionBase');
Rhaco::import('MixiBrowser');
Rhaco::import('arbo.network.browser.MixiBrowser');
/**
 * CustomfeedMixiListDiary
 *
 * @author  riaf <riafweb@gmail.com>
 * @package Conveyor
 * @version $Id: CustomfeedMixiListDiary.php 344 2007-10-26 18:10:10Z riafweb $
 */
class CustomfeedMixiListDiary extends SubscriptionBase
{
    function execute($rss){
        $rss20 = new Rss20();
        $rss20->setChannel(
            $this->variable('owner_id') . "'s mixi diary.",
            '',
            'http://mixi.jp/list_diary.pl?id=' . $this->variable('owner_id'),
            'ja'
        );

        $mixi = new MixiBrowser($this->variable('email'), $this->variable('password'));
        $list = $mixi->listDiary($this->variable('owner_id'));
        foreach($list as $i){
            $rssItem = new RssItem20($i['title'], $i['body'], $i['link'], $i['link']);
            $rssItem->setPubDate(strtotime(str_replace(array('年', '月', '日'), '/', $i['date'])));
            $rss20->setItem($rssItem);
        }

        return $this->merge($rss, $rss20);
    }

    function description(){
        return "mixiの日記一覧を取得";
    }

    function required(){
        return array(
            'MixiBrowser' => 'arbo',
        );
    }

    function config(){
        return array(
            'email'    => array('mixiのメールアドレス', 'text', '', true),
            'password' => array('mixiのパスワード', 'password', '', true),
            'owner_id' => array('取得する日記のユーザーID', 'text', '', true),
        );
    }
}

