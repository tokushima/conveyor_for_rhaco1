<?php
Rhaco::import('model.PublishBase');
Rhaco::import('network.mail.Mail');

/**
 * PublishMail
 *
 * @author  TSURUOKA Naoya <tsuruoka@labs.cybozu.co.jp>
 * @author  riaf <riafweb@gmail.com>
 * @package Conveyor
 * @version $Id: PublishMail.php 348 2007-10-28 07:03:28Z riafweb $
 */
class PublishMail extends PublishBase
{
    function execute($rss){
        $channel = $rss->getChannel();

        $message = '';
        $message .= $channel->getTitle() . "\n";
        $message .= $channel->getDescription() . "\n";

        foreach($rss->getItem() as $item){
            $entry = "";
            $keys = array(
                'author',
                'comments',
                'pubDate',
                'guid',
                'source',
                'enclosure',
                'title',
                'link',
                'description',
            );
            foreach($keys as $key){
                if(!empty($item->$key)){
                    $entry .= "{$key}: {$item->$key}\n";
                }
            }
            $message .= $entry . "\n";
        }

        $mail = new Mail();
        $mail->to($this->variable('to'));
        $mail->send($channel->getTitle(), $message);

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

    function rhacover() {
        return '1.4.1';
    }
}

