<?php
Rhaco::import('model.PublishBase');

/**
 * NotifyEject
 *
 * @author  riaf <riafweb@gmail.com>
 * @package Conveyor
 * @version $Id$
 */
class NotifyEject extends PublishBase
{
    function execute($rss){
        $items = $rss->getItem();
        if(!empty($items)){
            $this->eject($this->variable('platform'));
        }
        return $rss;
    }

    function eject($platform){
        switch(strtolower($platform)){
            case 'darwin':
                system('drutil eject');
                break;
            case 'mswin32':
                $com = new COM('WMPlayer.OCX');
                $com->cdromcollection->item[0]->eject();
                break;
            case 'freebsd':
                system('/usr/sbin/cdcontrol eject');
                break;
            case 'linux':
            default:
                system('eject');
        }
    }

    function config(){
        return array(
            'platform' => array('使用しているOS', 'select',
                array('darwin' => 'Mac OS X', 'freebsd' => 'Free BSD', 'linux' => 'Linux', 'mswin32' => 'Windows')
            ),
        );
    }

    function description(){
        return 'CDトレイを取り出します';
    }
}

