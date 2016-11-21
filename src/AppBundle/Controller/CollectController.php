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
        $user = $em->getRepository(User::class)->findOneById($this->getUser()->getId());

        if($tid === 'all'){
            foreach($user->getUserTiles() as $tile){
                if ($tile->getUser()->getUid() == $a && $tile->getCollected() + 86400 < time()) {
                    $tile->setCollected(time());
                    $bonus = 0;
                    foreach($tile->getTile()->getTileStructures() as $tileStructure){
                        $s = $tileStructure->getStructure();
                        if($s->getName() == 'nova_s' || $s->getName() == 'neutron_shield'){
                            $bonus += 5;
                        }elseif($s->getName() == 'nova_m' || $s->getName() == 'nova_l' || $s->getName() == 'electron_shield'){
                            $bonus += 10;
                        }elseif($s->getName() == 'outpost' || $s->getName() == 'proton_shield'){
                            $bonus += 20;
                        }elseif($s->getName() == 'infohub'){
                            $bonus += 30;
                        }elseif($s->getName() == 'starport'){
                            $bonus += 50;
                        }elseif($s->getName() == 'ethermine'){
                            $bonus += 100;
                        }
                    }

                    $stardust = $user->getUserResource('stardust');
                    $sdamount = $stardust->getAmount();
                    $stardust->setAmount($bonus + $sdamount + 40);
                }
            }
            $em->flush();
            return $this->forward('AppBundle:Collector:index', $_POST);
        }else{
            $tile = $em->getRepository(Tile::class)->findOneBy('tid', $tid);

            if ($tile->getUserTile()->getUser()->getUid() == $a && $tile->getUserTile()->getCollected() + 86400 < time()) {
                $tile->getUserTile()->setCollected(time());
                $bonus = 0;
                foreach($tile->getTileStructures() as $tileStructure){
                    $s = $tileStructure->getStructure();
                    if($s->getName() == 'nova_s' || $s->getName() == 'neutron_shield'){
                        $bonus += 5;
                    }elseif($s->getName() == 'nova_m' || $s->getName() == 'nova_l' || $s->getName() == 'electron_shield'){
                        $bonus += 10;
                    }elseif($s->getName() == 'outpost' || $s->getName() == 'proton_shield'){
                        $bonus += 20;
                    }elseif($s->getName() == 'infohub'){
                        $bonus += 30;
                    }elseif($s->getName() == 'starport'){
                        $bonus += 50;
                    }elseif($s->getName() == 'ethermine'){
                        $bonus += 100;
                    }
                }
                $stardust = $user->getUserResource('stardust');
                $sdamount = $stardust->getAmount();
                $stardust->setAmount($bonus + $sdamount + 40);
            } else {

                return $this->forward('AppBundle:Collector:index', $_POST);
            }

            $em->flush();
            return $this->forward('AppBundle:Collector:index', $_POST);
        }
        return $this->render('AppBundle:Collector:collect.html.twig',array('user' => $user));
    }
}