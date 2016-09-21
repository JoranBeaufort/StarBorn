<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use AppBundle\Entity\Tile;
use JoranBeaufort\Neo4jUserBundle\Entity\User;


class AttackController extends Controller
{
    public function indexAction(Request $request)
    {            
        $encoder = $this->get('nzo_url_encryptor');
        
        // user coords
        $uLat = $request->request->get('ulat');
        $uLng = $request->request->get('ulng');
        
        // tile centroid
        $t = $request->request->get('t');
        $w = $request->request->get('w');
        $tid = $request->request->get('tid');
                
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
        
        $em = $this->get('neo4j.graph_manager')->getClient();
        if($results[0]['val'] == $tid){
            $tile = $em->getRepository(Tile::class)->findOneBy('tid',$tid);
        }else{
             throw new \Exception('IDs dont match. Uiuiui!');
        }

        // set flash messages
        $fb = $this->get('session')->getFlashBag();

        if($w == 'primary'){
            $dmg = 10;
            $fb->add('success', true);
            $fb->add('success-message', $dmg.' Schaden verursacht!');
        }elseif($w == 'secondary'){
            $dmg = 50;
            $fb->add('success', true);
            $fb->add('success-message', $dmg.' Schaden verursacht!');
        }else{
            $dmg = 0;
        }


        foreach($tile->getTileStructures() as $ts){
            /* @var $ts \AppBundle\Entity\TileStructure */
            if($ts->getStructure()->getStructureType() == $t){
                $structure = $ts->getStructure();

                $fb->add('success-img',$structure->getImg());
                $hp = $ts->getHp();
                $hpNew = $hp-$dmg;
                if($hpNew <=0){
                    if($t == 'drone'){
                        
                        $tLat = $tile->getLat();
                        $tLng = $tile->getLng();
                        
                        $tileId = $tile->getTid();
                        $userId = $tile->getUserTile()->getUser()->getUid();  
                        
                        
                        $em->getDatabaseDriver()->run("MATCH(t:Tile{tid:'".$tileId."'})-[hs:HAS_STRUCTURE]->(s:Structure{type:'drone'}), (u:User{uid:'".$userId."'})-[c:CAPTURED]->(t) DELETE hs SET c.lost = ".time()." WITH c call apoc.refactor.setType(c, 'LOST') yield input, output return false"); 
                        
                        $q=   " UPDATE 
                                    gameField 
                                SET 
                                    rast = ST_SetValue(rast,2,ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326),2056),0)
                                WHERE 
                                    ST_Intersects(rast, ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326),2056));";
                        
                        $statement = $connection->prepare($q);
                        $statement->execute();
                        
                        $q=   " UPDATE 
                                    gameField 
                                SET 
                                    rast = ST_SetValue(rast,1,ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326),2056),0)
                                WHERE 
                                    ST_Intersects(rast, ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326),2056));";
                        
                        $statement = $connection->prepare($q);
                        $statement->execute();

                        /* @var $user \JoranBeaufort\Neo4jUserBundle\Entity\User */
                        $user = $em->getRepository(User::class)->findOneBy('uid',$this->getUser()->getUid());
                        $user->addXP(8);
                        
                        
                        $url = $this->generateUrl('map');
                        return new RedirectResponse($url);
                        
                    }else{
                        /* @var $user \JoranBeaufort\Neo4jUserBundle\Entity\User */
                        $user = $em->getRepository(User::class)->findOneBy('uid',$this->getUser()->getUid());
                        $user->addXP(8);

                        $tile->removeTileStructure($ts);
                    }     
                }else{
                    $ts->setHp($hpNew);
                }  
            }
        }


        $em->flush();
        $em->clear();

        return $this->forward('AppBundle:Scan:index',$_POST);

    }
}