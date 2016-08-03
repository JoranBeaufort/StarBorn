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
        $user = $this->getUser();               
        
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
        $tile = $em->getRepository(Tile::class)->findOneById(intval($tid));
        
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
                $tile->removeTileDrone($tile->getTileDrone());
            }else{
                $tile->getTileDrone()->setHp($hpNew);
            }     
            }else{
            $error = 'error';
        }
        
        $em->flush(); 
        
        return $this->render('AppBundle:Scan:scan.html.twig',array('uLat' => $uLat, 'uLng' => $uLng, 'a' => $a, 'user' => $user, 'tile' => $tile, "message" => $message));
        
    }
}