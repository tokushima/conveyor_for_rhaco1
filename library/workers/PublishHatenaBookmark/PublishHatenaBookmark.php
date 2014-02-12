<?php
Rhaco::import('model.PublishBase');
// Rhaco::import('tag.feed.Atom10');
Rhaco::import('network.http.Http');

/**
 * PublishHatenaBookmark
 *
 * @author  riaf <riafweb@gmail.com>
 * @package Conveyor
 * @version $Id: PublishHatenaBookmark.php 344 2007-10-26 18:10:10Z riafweb $
 */
class PublishHatenaBookmark extends PublishBase
{
    function execute($rss){
        $wsse = $this->_getWsse($this->variable('user'), $this->variable('pass'));
        foreach($rss->getItem() as $item){
            $headers = array(
                'Accept' => 'application/x.atom+xml, application/xml, text/xml, */*',
                'Authorization' => 'WSSE profile="UsernameToken"',
                'X-WSSE' => $wsse,
                'Content-Type' => 'application/x.atom+xml',
                'rawdata' => sprintf('<entry xmlns="http://purl.org/atom/ns#"><title>%s</title>'.
                    '<link rel="related" type="text/html" href="%s" /><summary type="text/plain">%s</summary></entry>',
                    $item->getTitle(), $item->getLink(), StringUtil::substring($item->getDescription(), 0, 100)
                ),
            );
            Http::post('http://b.hatena.ne.jp/atom/post', array(), $headers);
        }

        return $rss;
    }

    function description(){
        return 'はてなブックマークに登録する';
    }

    function config(){
        return array(
            'user' => array('ユーザーID', 'text', '', true),
            'pass' => array('パスワード', 'password', '', true),
        );
    }

    function _getWsse($user, $pass){
         $nowtime = date('Y-m-d\TH:i:s\Z');
         $nonce   = pack('H*', sha1(md5(time())));
         $digest  = base64_encode(pack('H*', sha1($nonce.$nowtime.$pass)));
         $wsse = 'UsernameToken Username="'.$user.'", PasswordDigest="'.$digest.'", Created="'.$nowtime.'", Nonce="'.base64_encode($nonce).'"';

        return $wsse;
    }
}
