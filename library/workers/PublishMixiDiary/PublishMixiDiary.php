<?php
Rhaco::import('model.PublishBase');
Rhaco::import('tag.model.SimpleTag');
Rhaco::import('arbo.network.browser.MixiBrowser');

/**
 * PublishMixiDiary
 *
 * @author  riaf <riafweb@gmail.com>
 * @package Conveyor
 * @version $Id: PublishMixiDiary.php 401 2008-02-10 17:57:52Z riafweb $
 */
class PublishMixiDiary extends PublishBase
{
    function execute($rss){
        $mixi = new MixiBrowser($this->variable('email'), $this->variable('password'));
        foreach($rss->getItem() as $item){
            $mixi->addDiary($item->getTitle(), TemplateFormatter::getCdata($item->getDescription()));
            sleep(0.5);
        }
        return $rss;
    }

    function description(){
        return 'mixi日記にpostする';
    }

    function config(){
        return array(
            'email' => array('メールアドレス', 'text', '', true),
            'password' => array('パスワード', 'password', '', true),
        );
    }

    function required(){
        return array('MixiBrowser' => 'arbo');
    }
}

