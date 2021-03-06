<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Structure;
use AppBundle\Entity\Tile;
use JoranBeaufort\Neo4jUserBundle\Entity\User;

class UpgradeController extends Controller
{
    public function indexAction(Request $request)
    {
        /*
        if($this->getUser()->getUsername() != 'mfbaer'){
            echo "work in progress on this page"; die;
        }
        */
        $encoder = $this->get('nzo_url_encryptor');

        // user coords
        $uLat = $request->request->get('ulat');
        $uLng = $request->request->get('ulng');

        // tile centroid
        $bid = $request->request->get('bid');
        $tid = $request->request->get('tid');
        $sid = $request->request->get('sid');
        $nid = $request->request->get('nid');

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

            $em = $this->get('neo4j.graph_manager')->getClient();
            $em->clear();
            $tile = $em->getRepository(Tile::class)->findOneBy('tid',$tid);
            $structureNew =  $em->getRepository(Structure::class)->findOneBy('sid',intval($nid));

            $upgradeable = true;
            foreach($tile->getTileStructures() as $ts){
                if($ts->getStructure()->getSid() == $structureNew->getSid()){
                    $upgradeable = false;
                }
            }

            if($upgradeable == true){
                $q = "MATCH (u:User{uid:'".$this->getUser()->getUid()."'})-[:HAS_INVENTORY]->(i)-[c:CONTAINS]->(b:Blueprint{bid:".$bid."})-[:BUILDS]->(sn:Structure), (t:Tile{tid:'".$tid."'})-[hs:HAS_STRUCTURE]->(s:Structure{sid:".$sid."}) DELETE hs CREATE (t)-[hsn:HAS_STRUCTURE]->(sn) SET u.xp=(TOINT(u.xp)+6), hsn.hp=sn.hp, c.amount = (TOINT(c.amount)-1) return c.amount as amount";
                $nr = $em->getDatabaseDriver()->run($q);
                // var_dump($nr->firstRecord()->get('amount'));die;

                if (intval($nr->firstRecord()->get('amount')) < 1) {
                    $em->getDatabaseDriver()->run("MATCH (u:User{uid:'".$this->getUser()->getUid()."'})-[:HAS_INVENTORY]->(i)-[c:CONTAINS]->(b:Blueprint{bid:".$bid."}) DELETE c");
                }
                // set flash messages
                $fb = $this->get('session')->getFlashBag();
                $fb->add('success', true);
                $fb->add('success-message', $structureNew->getName_DE() . ' wurde erfolgreich gebaut!');
                $fb->add('success-img',$structureNew->getImg());
            }
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

            $buildable = array('drone'=>null, 'building'=>null, 'shield'=>null);

            if(count($tile->getTileStructures()) == 2){
                $buildable['shield'] = true;
            }elseif(count($tile->getTileStructures()) == 1){
                $buildable['building'] = true;
            }

            return $this->forward('AppBundle:Build:index');

        }else{
            throw new \Exception('IDs dont match. Uiuiui!');
        }
    }
}