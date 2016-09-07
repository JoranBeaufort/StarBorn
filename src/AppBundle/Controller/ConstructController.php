<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Structure;
use AppBundle\Entity\Tile;
use AppBundle\Entity\Blueprint;
use JoranBeaufort\Neo4jUserBundle\Entity\User;
use AppBundle\Entity\Resources;
use AppBundle\Entity\Inventory;
use AppBundle\Entity\UserResource;



class ConstructController extends Controller
{
    public function indexAction(Request $request)
    {
        $encoder = $this->get('nzo_url_encryptor');

        // user coords
        $uLat = $request->request->get('ulat');
        $uLng = $request->request->get('ulng');

        // tile centroid
        $bid = $request->request->get('bid');
        $tid = $request->request->get('tid');
        $sid = $request->request->get('sid');

        $a = $encoder->decrypt($request->request->get('a'));

        // Get the node ID
        $em_pgsql = $this->getDoctrine()->getManager();
        $connection = $em_pgsql->getConnection();
        $q=   " SELECT 
                    rid, 
                    ST_Value(rast, 3, ST_Transform(ST_SetSRID(ST_MakePoint(".$uLng.",".$uLat."),4326),2056),false) val 
                FROM 
                    gameField 
                WHERE
                    ST_Intersects(rast, 3, ST_Transform(ST_SetSRID(ST_MakePoint(".$uLng.",".$uLat.") ,4326),2056))";

        $statement = $connection->prepare($q);
        $statement->execute();
        $results = $statement->fetchAll();

        if($results[0]['val'] == $tid){
            /**
             * @var $structure \AppBundle\Entity\Structure
             * @var $tile \AppBundle\Entity\Tile
             * @var $blueprint \AppBundle\Entity\Blueprint
             * @var $user \JoranBeaufort\Neo4jUserBundle\Entity\User
             */

            $message = array();

            $em = $this->get('neo4j.graph_manager')->getClient();

            $tile = $em->getRepository(Tile::class)->findOneBy('tid',$tid);
            $structure =  $em->getRepository(Structure::class)->findOneBy('sid',intval($sid));


            $typeLocked = false;
            foreach( $tile->getTileStructures() as $tilestructure) {
                if ($tilestructure->getStructure()->getStructureType() == $structure->getStructureType()) {
                    $typeLocked = true;
                }
            }

            $user = $em->getRepository(User::class)->findOneBy('uid',$this->getUser()->getUid());

            if($typeLocked == false) {
                $tile->addTileStructure($structure);
                $blueprintInventory = $user->getUserInventory()->getInventory()->getBlueprintInventoryByBid($bid);

                if ($blueprintInventory->getAmount() == 1) {
                    $user->getUserInventory()->getInventory()->removeBlueprintInventory($blueprintInventory);
                } else {
                    $amount = $blueprintInventory->getAmount();
                    $amountNew = $amount - 1;
                    $blueprintInventory->setAmount($amountNew);
                }
                $em->flush();

                $message['type'] = 'constructionComplete';
                $message['text'] = $structure->getName_DE().' wurde erfolgreich gebaut!';
                $message['img'] = $structure->getImg();
            }else{
                $message = null;
            }

            $structures = array('drone' => null, 'building' => null, 'shield' => null);

            foreach($tile->getTileStructures() as $ts){
                if($ts->getStructure()->getStructureType() == 'drone'){
                    $structures['drone'] = $ts;
                }
                if($ts->getStructure()->getStructureType() == 'building'){
                    $structures['building'] = $ts;

                }
                if($ts->getStructure()->getStructureType() == 'shield'){
                    $structures['shield'] = $ts;
                }
            }

            $buildable = array('drone'=>null, 'building'=>null, 'shield'=>null);

            if(count($tile->getTileStructures()) == 2){
                $buildable['shield'] = true;
            }elseif(count($tile->getTileStructures()) == 1){
                $buildable['building'] = true;
            }


            $user = $em->getRepository(User::class)->findOneBy('uid',$this->getUser()->getUid());
            $em->clear();

            // var_dump(count($user->getUserInventory()->getInventory()->getBlueprintInventoriesByType('building')));die;
            return $this->render('AppBundle:Build:build.html.twig',array('uLat' => $uLat, 'uLng' => $uLng, 'a' => $a, 'user' => $user, 'tile' => $tile, 'structures' => $structures, 'buildable' => $buildable, 'message' => $message));

        }else{
            throw new \Exception('IDs dont match. Uiuiui!');
        }
    }
}