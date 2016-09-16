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
            $dmg = 10;
            $message['text'] = $dmg.' Schaden verursacht!';
        }elseif($w == 'secondary'){
            $dmg = 50;
            $message['text'] = $dmg.' Schaden verursacht!';
        }


        foreach($tile->getTileStructures() as $ts){
            /* @var $ts \AppBundle\Entity\TileStructure */
            if($ts->getStructure()->getStructureType() == $t){
                $structure = $ts->getStructure();
                $message['img'] = $structure->getImg();
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

        $structures = array();
        $attackable = array('drone'=>null, 'building'=>null, 'shield'=>null);

        foreach($tile->getTileStructures() as $ts){
            if($ts->getStructure()->getStructureType() == 'drone'){
                array_push($structures, $ts);
                $attackable['drone'] = true;

            }
            if($ts->getStructure()->getStructureType() == 'building'){
                array_push($structures, $ts);
                $attackable['building'] = true;
            }
            if($ts->getStructure()->getStructureType() == 'shield'){
                array_push($structures, $ts);
                $attackable['shield'] = true;
            }
        }

        if($attackable['drone'] == true &&  $attackable['building'] == true && $attackable['shield'] = true){
            $attackable['building'] = false;
            $attackable['drone'] = false;
        }elseif(($attackable['drone'] == true &&  $attackable['building'] == true) || ($attackable['drone'] == true &&  $attackable['shield'] == true)){
            $attackable['drone'] = false;
        }

        /* @var $user \JoranBeaufort\Neo4jUserBundle\Entity\User */
        $user = $em->getRepository(User::class)->findOneBy('uid',$this->getUser()->getUid());
        return $this->render('AppBundle:Scan:scan.html.twig',array('uLat' => $uLat, 'uLng' => $uLng, 'a' => $a, 'user' => $user, 'tile' => $tile, 'structures' => $structures, 'attackable' => $attackable, 'message' => $message));
        
    }
}