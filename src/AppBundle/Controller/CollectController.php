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

        $ethertoken = 0;
        $stardust = 0;
        $tidarray = array();

        if($tid === 'all'){

            foreach($user->getUserTiles() as $tile){

                if ($tile->getUser()->getUid() == $a && $tile->getCollected() + 86400 < time()) {
                    array_push($tidarray,"'".$tile->getTile()->getTid()."'");
                    foreach($tile->getTile()->getTileStructures() as $tileStructure){
                        $s = $tileStructure->getStructure();
                        if($s->getName() == 'nova_s' || $s->getName() == 'neutron_shield'){
                            $stardust += 5;
                        }elseif($s->getName() == 'nova_m' || $s->getName() == 'nova_l' || $s->getName() == 'electron_shield'){
                            $stardust += 10;
                        }elseif($s->getName() == 'outpost' || $s->getName() == 'proton_shield'){
                            $stardust += 20;
                        }elseif($s->getName() == 'infohub'){
                            $stardust += 30;
                        }elseif($s->getName() == 'starport'){
                            $stardust += 50;
                        }elseif($s->getName() == 'starmine'){
                            $stardust += 100;
                        }elseif($s->getName() == 'ethermine'){
                            $stardust += 40;
                            $ethertoken += 1;
                        }
                    }
                    $stardust += 40;
                }
            }
        }else{
            $tile = $em->getRepository(Tile::class)->findOneBy('tid', $tid);

            if ($tile->getUserTile()->getUser()->getUid() == $a && $tile->getUserTile()->getCollected() + 86400 < time()) {
                array_push($tidarray,"'".$tile->getTid()."'");
                foreach($tile->getTileStructures() as $tileStructure){
                    $s = $tileStructure->getStructure();
                    if($s->getName() == 'nova_s' || $s->getName() == 'neutron_shield'){
                        $stardust += 5;
                    }elseif($s->getName() == 'nova_m' || $s->getName() == 'nova_l' || $s->getName() == 'electron_shield'){
                        $stardust += 10;
                    }elseif($s->getName() == 'outpost' || $s->getName() == 'proton_shield'){
                        $stardust += 20;
                    }elseif($s->getName() == 'infohub'){
                        $stardust += 30;
                    }elseif($s->getName() == 'starport'){
                        $stardust += 50;
                    }elseif($s->getName() == 'starmine'){
                        $stardust += 100;
                    }elseif($s->getName() == 'ethermine'){
                        $stardust += 40;
                        $ethertoken += 1;
                    }
                }
                $stardust += 40;
            }
        }

        if(intval($stardust) != 0){
            $tids = join(',',$tidarray);
            $q = "MATCH (u:User{uid:'".$this->getUser()->getUid()."'})-[c:CAPTURED]->(t:Tile) WHERE t.tid in [".$tids."] set c.collected = ".time();
            $em->getDatabaseDriver()->run($q);
            $q="match (u:User{uid:'".$this->getUser()->getUid()."'})-[hrs:HAS_RESOURCE]->(rs:Resources{name:'stardust'}), (u)-[hre:HAS_RESOURCE]->(re:Resources{name:'ethertoken'}) SET hrs.amount = (TOINT(hrs.amount)+TOINT(".$stardust.")), hre.amount = (TOINT(hre.amount)+TOINT(".$ethertoken."))";
            $em->getDatabaseDriver()->run($q);
        }


        return $this->forward('AppBundle:Collector:index');
    }
}