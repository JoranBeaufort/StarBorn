<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Entity\Blueprint;
use JoranBeaufort\Neo4jUserBundle\Entity\User;



class BuyController extends Controller
{
    public function indexAction(Request $request)
    {
        /**
         * @var $blueprint \AppBundle\Entity\Blueprint
         * @var $user \JoranBeaufort\Neo4jUserBundle\Entity\User
         */
/*
        if($this->getUser()->getUsername() != 'mfbaer'){
            echo "I am currently working on this page and apologise for the inconvenience. Check back soon!"; die;
        }
*/

        $encoder = $this->get('nzo_url_encryptor');

        // tile centroid
        $amount = $request->request->get('amount');
        if($amount <= 0 || $amount == null){
            $amount = 1;
        }
        $bid = $request->request->get('bid');
        $uid = $encoder->decrypt($request->request->get('a'));

        $em = $this->get('neo4j.graph_manager')->getClient();

        if ($uid == $this->getUser()->getUid()) {

            $user = $em->getRepository(User::class)->findOneBy('uid', $this->getUser()->getUid());
            $blueprint = $em->getRepository(Blueprint::class)->findOneBy('bid', intval($bid));

            $buyable = true;
            $resAmount = array();
            $resAmount['stardust'] = 0;
            $resAmount['ethertoken'] = 0;
            foreach ($blueprint->getBlueprintResources() as $resource) {
                $r = $user->getUserResource($resource->getResources()->getName());
                $rAmount = $r->getAmount();
                $resAmount[$resource->getResources()->getName()] = intval($resource->getAmount()) * intval($amount);
                if(intval($rAmount) - (intval($resource->getAmount()) * intval($amount)) < 0){
                    $buyable = false;
                }

            }

            if($buyable === true){

                if ($user->getUserInventory()->getInventory()->getBlueprintInventoryByBid($bid) != null) {
                    $q = "MATCH (u:User{uid:'".$this->getUser()->getUid()."'})-[:HAS_INVENTORY]->(i)-[c:CONTAINS]->(b:Blueprint{bid:".$bid."}), (u)-[hrs:HAS_RESOURCE]->(rs:Resources{name:'stardust'}), (u)-[hre:HAS_RESOURCE]->(re:Resources{name:'ethertoken'}) SET hrs.amount = (TOINT(hrs.amount)-TOINT(".$resAmount['stardust'].")), hre.amount = (TOINT(hre.amount)-TOINT(".$resAmount['ethertoken'].")), c.amount = (TOINT(c.amount)+TOINT(".$amount."))";
                    $em->getDatabaseDriver()->run($q);
                } else {
                    $q = "MATCH (u:User{uid:'".$this->getUser()->getUid()."'})-[:HAS_INVENTORY]->(i), (b:Blueprint{bid:".$bid."}), (u)-[hrs:HAS_RESOURCE]->(rs:Resources{name:'stardust'}), (u)-[hre:HAS_RESOURCE]->(re:Resources{name:'ethertoken'})  create (i)-[c:CONTAINS]->(b) SET hrs.amount = (TOINT(hrs.amount)-TOINT(".$resAmount['stardust'].")), hre.amount = (TOINT(hre.amount)-TOINT(".$resAmount['ethertoken'].")), c.amount = TOINT(".$amount.")";
                    $em->getDatabaseDriver()->run($q);

                }
                // set flash messages
                $fb = $this->get('session')->getFlashBag();
                $fb->add('success', true);
                $fb->add('success-message', $blueprint->getName_DE() . ' wurde erfolgreich gekauft!');
                $fb->add('success-img','/img/toy-576520.svg');
            }else{
                // set flash messages
                $fb = $this->get('session')->getFlashBag();
                $fb->add('error', true);
                $fb->add('error-message', 'Du hast nicht genügend Ressourcen um diese Menge zu kaufen!');
                $fb->add('error-img','/img/toy-576520.svg');
            }

            return $this->forward('AppBundle:Store:index');
        } else {
            throw new \Exception('IDs dont match. Uiuiui!');
        }
    }
}
