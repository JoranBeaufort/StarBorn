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

//var_dump("1: ".time());
        // user coords
        $uLat = $request->request->get('ulat');
        $uLng = $request->request->get('ulng');

        // tile centroid
        $bid = $request->request->get('bid');
        $tid = $request->request->get('tid');
        $sid = $request->request->get('sid');

        $a = $encoder->decrypt($request->request->get('a'));
        //var_dump("2: ".time());
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
        //var_dump("3: ".time());
        if($results[0]['val'] == $tid){
            /**
             * @var $structure \AppBundle\Entity\Structure
             * @var $tile \AppBundle\Entity\Tile
             * @var $blueprint \AppBundle\Entity\Blueprint
             * @var $user \JoranBeaufort\Neo4jUserBundle\Entity\User
             */
            //var_dump("4: ".time());

            $em = $this->get('neo4j.graph_manager')->getClient();
            $em->clear();
            //var_dump("5: ".time());

            $tile = $em->getRepository(Tile::class)->findOneBy('tid',$tid);
            $structure =  $em->getRepository(Structure::class)->findOneBy('sid',intval($sid));
            //var_dump("6: ".time());


            $typeLocked = false;
            foreach( $tile->getTileStructures() as $tilestructure) {
                if ($tilestructure->getStructure()->getStructureType() == $structure->getStructureType()) {
                    $typeLocked = true;
                }
            }
            //var_dump("7: ".time());


            if($typeLocked == false) {
                $nr = $em->getDatabaseDriver()->run("match (u:User{uid:'".$this->getUser()->getUid()."'})-[:HAS_INVENTORY]->(i)-[c:CONTAINS]->(b:Blueprint{bid:".$bid."}), (t:Tile{tid:'" . $tid . "'}), (s:Structure{sid:".intval($sid)."}) create (t)-[hs:HAS_STRUCTURE]->(s) set hs.hp = s.hp, u.xp = (u.xp+6) return c.amount as amount");
                //var_dump("8: ".time());

                if (intval($nr->firstRecord()->get('amount')) <= 1) {
                    $em->getDatabaseDriver()->run("match (u:User{uid:'".$this->getUser()->getUid()."'})-[:HAS_INVENTORY]->(i)-[c:CONTAINS]->(b:Blueprint{bid:".$bid."}) delete c");
                } else {
                    $em->getDatabaseDriver()->run("match (u:User{uid:'".$this->getUser()->getUid()."'})-[:HAS_INVENTORY]->(i)-[c:CONTAINS]->(b:Blueprint{bid:".$bid."}) set c.amount = (TOINT(c.amount)-TOINT(1))");
                }
                //var_dump("9: ".time());

                // set flash messages
                $fb = $this->get('session')->getFlashBag();
                $fb->add('success', true);
                $fb->add('success-message', $structure->getName_DE() . ' wurde erfolgreich gebaut!');
                $fb->add('success-img',$structure->getImg());

            }
            //var_dump("10: ".time());


            $em->clear();
            $tile = $em->getRepository(Tile::class)->findOneBy('tid',$tid);

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
            //var_dump("11: ".time());

            $buildable = array('drone'=>null, 'building'=>null, 'shield'=>null);

            if(count($tile->getTileStructures()) == 2){
                $buildable['shield'] = true;
            }elseif(count($tile->getTileStructures()) == 1){
                $buildable['building'] = true;
            }
            //var_dump("12: ".time());

            return $this->forward('AppBundle:Build:index');
        }else{
            throw new \Exception('IDs dont match. Uiuiui!');
        }
    }
}