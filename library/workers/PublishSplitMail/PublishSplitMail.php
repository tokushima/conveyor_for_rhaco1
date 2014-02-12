<?php
Rhaco::import('model.PublishBase');
Rhaco::import('network.mail.Mail');

/**
 * PublishSplitMail
 *
 * @author  riaf <riafweb@gmail.com>
 * @package Conveyor
 * @version $Id: /workers/PublishMail/PublishMail.php 880 2007-10-26T18:09:03.762450Z riaf  $
 */
class PublishSplitMail extends PublishBase
{
    function execute($rss){
        $mail = new Mail();
        $mail->to($this->variable('to'));

        foreach($rss->getItem() as $item){
            $mail->send($item->getTitle(), $item->getDescription());
        }

        return $rss;
    }

    function description(){
        return 'メールを送信する';
    }

    function config(){
        return array(
            'to' => '送信先のアドレス',
        );
    }

    function rhacover(){
        return '1.4.1';
    }
}

