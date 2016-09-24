<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Tile;
use JoranBeaufort\Neo4jUserBundle\Entity\User;

class CollectController extends Controller
{
    public function indexAction(Request $request)
    {
        $encoder = $this->get('nzo_url_encryptor');
        $a = $encoder->decrypt($request->request->get('a'));
        $tid = $request->request->get('tid');

        $em = $this->get('neo4j.graph_manager')->getClient();
        $em->clear();
        /**
         *  @var $tile \AppBundle\Entity\Tile
         *  @var $user \JoranBeaufort\Neo4jUserBundle\Entity\User
         */

        $tile = $em->getRepository(Tile::class)->findOneBy('tid',$tid);
        $user = $em->getRepository(User::class)->findOneById($this->getUser()->getId());

        if($tile->getUserTile()->getUser()->getUid() == $a && $tile->getUserTile()->getCollected()+86400 < time() ){
            $tile->getUserTile()->setCollected(time());
            $stardust = $user->getUserResource('stardust');
            $sdamount = $stardust->getAmount();
            $stardust->setAmount($sdamount+40);
            var_dump($stardust->getAmount());
        }else{
            throw new \Exception('IDs dont match. Uiuiui!');
        }

        $em->flush();
        return $this->forward('AppBundle:Collector:index',$_POST);
    }
}