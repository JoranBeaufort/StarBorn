<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;

use AppBundle\Form\CaptureInterfaceType;
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
        
        $message = array();
        $message['type'] = 'as';
        
        if($w == 'primary'){
            $dmg = 1;
            $message['text'] = $dmg.' Schaden verursacht!';
        }elseif($w == 'secondary'){
            $dmg = 2;
            $message['text'] = $dmg.' Schaden verursacht!';
        }

        if($t == "shield"){
            $structure = $tile->getTileShield()->getShield();
            $message['img'] = $structure->getImg();
            $hp = $tile->getTileShield()->getHp();
            $hpNew = $hp-$dmg;
            if($hpNew <=0){
                $tile->removeTileShield($tile->getTileShield());
            }else{
                $tile->getTileShield()->setHp($hpNew);
            }            
        }elseif($t == "building"){
            $structure = $tile->getTileBuilding()->getBuilding();
            $message['img'] = $structure->getImg();
            $hp = $tile->getTileBuilding()->getHp();
            $hpNew = $hp-$dmg;
            if($hpNew <=0){
                $tile->removeTileBuilding($tile->getTileBuilding());
            }else{
                $tile->getTileBuilding()->setHp($hpNew);
            }
        }elseif($t == "drone"){
            $structure = $tile->getTileDrone()->getDrone();
            $message['img'] = $structure->getImg();
            $hp = $tile->getTileDrone()->getHp();
            $hpNew = $hp-$dmg;
            if($hpNew <=0){
                
                $tLat = $tile->getLat();
                $tLng = $tile->getLng();
                
                $tileId = $tile->getTid();
                $userId = $tile->getUserTile()->getUser()->getUid();  
                
                //$drone = $tile->getTileDrone();
                //$tile = $tile->removeTileDrone($drone);
                

                //$user = $user->removeUserTile($tile);                

                //$user = $user->addUserTileLost($tile,time());
                
                
                $em->getDatabaseDriver()->run("MATCH(t:Tile{tid:'".$tileId."'})-[hd:HAS_DRONE]->(d:Drone), (u:User{uid:'".$userId."'})-[c:CAPTURED]->(s) DELETE hd SET c.lost = ".time()." WITH c call apoc.refactor.setType(c, 'LOST') yield input, output return false"); 
                
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
                
                
                $url = $this->generateUrl('map');
                return new RedirectResponse($url);
                
            }else{
                $tile->getTileDrone()->setHp($hpNew);
            }     
            }else{
            $error = 'error';
        }
        $em->flush();
        
        $user = $em->getRepository(User::class)->findOneBy('uid',$this->getUser()->getUid());
        return $this->render('AppBundle:Scan:scan.html.twig',array('uLat' => $uLat, 'uLng' => $uLng, 'a' => $a, 'user' => $user, 'tile' => $tile, 'message' => $message));
        
    }
}