<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Form\CaptureInterfaceType;
use AppBundle\Entity\Tile;
use AppBundle\Entity\Structure;
use JoranBeaufort\Neo4jUserBundle\Entity\User;

class CaptureController extends Controller
{
    public function indexAction(Request $request)
    {
        // var_dump("1 ".time());
        $encoder = $this->get('nzo_url_encryptor');
        
        // user coords
        $uLat = $request->request->get('ulat');
        $uLng = $request->request->get('ulng');
        
        // tile centroid
        $tLat = $request->request->get('tlat');
        $tLng = $request->request->get('tlng');
        
        // tile BBOX
        $tblx = $request->request->get('tblx');
        $tbly = $request->request->get('tbly');
        $ttlx = $request->request->get('ttlx');
        $ttly = $request->request->get('ttly');
        $ttrx = $request->request->get('ttrx');
        $ttry = $request->request->get('ttry');
        $tbrx = $request->request->get('tbrx');
        $tbry = $request->request->get('tbry');
        // var_dump("2 ".time());
        $bBox = '['.$tblx.','.$tbly.'],['.$ttlx.','.$ttly.'],['.$ttrx.','.$ttry.'],['.$tbrx.','.$tbry.']';
        // // var_dump($bBox);die;
        // // var_dump($uLat);die;
       
        $a = $encoder->decrypt($request->request->get('a'));
        
        $form = $this->createForm(CaptureInterfaceType::class);
        
        $form->handleRequest($request);

        // var_dump("3 ".time());
        if ($form->isSubmitted() && $form->isValid()) {
            // var_dump("4 ".time());
            /* @var $user \JoranBeaufort\Neo4jUserBundle\Entity\User */
                $em = $this->get('neo4j.graph_manager')->getClient();
                
                // user coords
                $uLat = $request->request->get('ulat');
                $uLng = $request->request->get('ulng');
                
                // tile centroid
                $tLat = $request->request->get('tlat');
                $tLng = $request->request->get('tlng'); 

                $bBox = $request->request->get('bBox');

            // var_dump("5 ".time());
                $em_pgsql = $this->getDoctrine()->getManager();
                $connection = $em_pgsql->getConnection();
                $q=   " SELECT 
                            rid, 
                            ST_Value(rast, 1, ST_Transform(ST_SetSRID(ST_MakePoint(".$uLng.",".$uLat."),4326),2056),false) val 
                        FROM 
                            gameField 
                        WHERE
                            ST_Intersects(rast, 1, ST_Transform(ST_SetSRID(ST_MakePoint(".$uLng.",".$uLat.") ,4326),2056))";
                
                $statement = $connection->prepare($q);
                $statement->execute();
                $results = $statement->fetchAll();
            // var_dump("6 ".time());
                if(!$results || $results[0]['val'] === '0' || $results[0]['val'] === 0){                    
                    $treasure = null;
                    // get potential resources
                    $landcover = $form->get('landcover')->getData();
                    
                    $landcover = join(',', $landcover);
                    
                    $em->clear();

                    $hreb=0;
                    $hrsb=0;

                    $q=   " SELECT 
                                rid, 
                                ST_Value(rast, 4, ST_Transform(ST_SetSRID(ST_MakePoint(".$uLng.",".$uLat."),4326),2056),false) val 
                            FROM 
                                gameField 
                            WHERE
                                ST_Intersects(rast, 4, ST_Transform(ST_SetSRID(ST_MakePoint(".$uLng.",".$uLat.") ,4326),2056))";

                    $statement = $connection->prepare($q);
                    $statement->execute();
                    $results = $statement->fetchAll();

                    if($results[0]['val'] == 1){
                        $hreb=5;
                        $hrsb=1500;
                        $treasure = 's';
                    }elseif($results[0]['val'] == 2){
                        $hreb=10;
                        $hrsb=3000;
                        $treasure = 'm';
                    }elseif($results[0]['val'] == 3) {
                        $hreb = 15;
                        $hrsb = 6000;
                        $treasure = 'l';
                    }

                if($results[0]['val'] != 0){
                    $q=   " UPDATE 
                                gameField 
                            SET 
                                rast = ST_SetValue(rast,4,ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326),2056),0)
                            WHERE 
                                ST_Intersects(rast, ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326),2056));";
                    $statement = $connection->prepare($q);
                    $statement->execute();

                    $q=   " UPDATE
                                treasure
                            SET 
                                timefound = ".time().",
                                uint = ".$this->getUser()->getUint()."
                            FROM    
                               (SELECT treasure.*
                                FROM treasure,
                                  (select ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326) as poi) as poi
                                WHERE ST_DWithin(geom, poi, 300)
                                ORDER BY ST_Distance(geom, poi)
                                LIMIT 1) as u
                            WHERE treasure.tid = u.tid";
                    $statement = $connection->prepare($q);
                    $statement->execute();

                }


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
                    // var_dump("9 ".time());

                    // get the tile and create the things
                    if($results[0]['val'] != -9999){
                        $tid=$results[0]['val'];
                        $em->getDatabaseDriver()->run("match (s:Structure{name:'nova_xs'}),(u:User{uid:'".$this->getUser()->getUid()."'})-[hrs:HAS_RESOURCE]->(rs:Resources{name:'stardust'}), (u)-[hre:HAS_RESOURCE]->(re:Resources{name:'ethertoken'}), (t:Tile{tid:'".$tid."'}) create (u)-[c:CAPTURED{landcover:'".$landcover."', captured:'".time()."', collected:'".time()."'}]->(t) create (t)-[hs:HAS_STRUCTURE{hp:'10'}]->(s) set u.xp=(u.xp+4), hrs.amount=(hrs.amount+50+".$hrsb."), hre.amount=(hre.amount+1+".$hreb.")");
                    }else{
                        $tokenGenerator=$this->get('tile.token_generator');
                        $tid=$tokenGenerator->generateTileToken(9);
                        $em->getDatabaseDriver()->run("match (s:Structure{name:'nova_xs'}), (u:User{uid:'".$this->getUser()->getUid()."'})-[hrs:HAS_RESOURCE]->(rs:Resources{name:'stardust'}), (u)-[hre:HAS_RESOURCE]->(re:Resources{name:'ethertoken'}) create (t:Tile{tid:'".$tid."', rid:'".$results[0]['rid']."', tLat:'".$tLat."', tLng:'".$tLng."', bBox:'".$bBox."'}), (u)-[c:CAPTURED{landcover:'".$landcover."', captured:'".time()."', collected:'".time()."'}]->(t), (t)-[hs:HAS_STRUCTURE{hp:'10'}]->(s) set u.xp=(u.xp+4), hrs.amount=(hrs.amount+50+".$hrsb."), hre.amount=(hre.amount+1+".$hreb.")");
                    }


                    $q=   " UPDATE 
                                gameField 
                            SET 
                                rast = ST_SetValue(rast,1,ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326),2056),".$this->getUser()->getUint().")
                            WHERE 
                                ST_Intersects(rast, ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326),2056));";
                   
                    $statement = $connection->prepare($q);
                    $statement->execute();
                    // var_dump("12 ".time());
                    
                    $q=   " UPDATE 
                                gameField 
                            SET 
                                rast = ST_SetValue(rast,2,ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326),2056),".$this->getUser()->getUserTeam()->getTeam()->getTid().")
                            WHERE 
                                ST_Intersects(rast, ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326),2056));";
                   
                    $statement = $connection->prepare($q);
                    $statement->execute();
                    // var_dump("13 ".time());
                    
                    $q=   " UPDATE 
                                gameField 
                            SET 
                                rast = ST_SetValue(rast,3,ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326),2056),".$tid.")
                            WHERE 
                                ST_Intersects(rast, ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326),2056));";
                   
                    $statement = $connection->prepare($q);
                    $statement->execute();
                    // var_dump("14 ".time());
                    
                    if($treasure){
                        return $this->render('AppBundle:Capture:success_treasure.html.twig',array('user' => $this->getUser(),'treasure' => $treasure));
                    }else{
                        return $this->render('AppBundle:Capture:success.html.twig',array('user' => $this->getUser()));
                    }
                }elseif($results[0]['val'] !== '0' || $results[0]['val'] !== 0){
                    $user = $this->getUser();
                    $tileUser = $em->getRepository(User::class)->findOneBy('uint',intval($results[0]['val']));
                    if($user ===  $tileUser){
                        $error = null;
                        $info = 'Dieses Gebiet gehört bereits dir!';     
                    }else{
                        $error = 'Gebiet wurde bereits Eingenommen.';
                        if($user->getUserTeam()->getTeam()->getTid() ===  $tileUser->getUserTeam()->getTeam()->getTid()){
                            $info = 'Dieses Gebiet gehört bereits deinem Team!';
                        }else{
                            $info = 'Dieses Gebiet gehört dem gegnerischen Team. Du musst dieses Gebiet angreifen bevor du es einnehmen kannst!';
                        }
                    }
                    return $this->render('AppBundle:Capture:error.html.twig',array('user' => $user, 'error' =>$error, 'info'=>$info));
                }
                
            
        }else{      
            $em_pgsql = $this->getDoctrine()->getManager();
            $em = $this->get('neo4j.graph_manager')->getClient();
            $connection = $em_pgsql->getConnection();
            $q=   " SELECT 
                        rid, 
                        ST_Value(rast, 1, ST_Transform(ST_SetSRID(ST_MakePoint(".$uLng.",".$uLat."),4326),2056),false) val 
                    FROM 
                        gameField 
                    WHERE
                        ST_Intersects(rast, 1, ST_Transform(ST_SetSRID(ST_MakePoint(".$uLng.",".$uLat.") ,4326),2056))";
            
            $statement = $connection->prepare($q);
            $statement->execute();
            $results = $statement->fetchAll();   
            if($results[0]['val'] != '0' || $results[0]['val'] != 0){
                $user = $this->getUser();
                $tileUser = $em->getRepository(User::class)->findOneBy('uint',intval($results[0]['val']));
                if($user ===  $tileUser){
                    $error = null;
                    $info = 'Dieses Gebiet gehört bereits dir!';     
                }else{
                    $error = 'Gebiet wurde bereits Eingenommen.';     
                    if($user->getUserTeam()->getTeam()->getTid() ===  $tileUser->getUserTeam()->getTeam()->getTid()){
                        $info = 'Dieses Gebiet gehört bereits deinem Team!';
                    }else{
                        $info = 'Dieses Gebiet gehört dem gegnerischen Team. Du musst dieses Gebiet angreifen bevor du es einnehmen kannst!';
                    }
                }
                return $this->render('AppBundle:Capture:error.html.twig',array('user' => $user, 'error' =>$error, 'info'=>$info));
            }            
            return $this->render('AppBundle:Capture:capture.html.twig',array('uLat' => $uLat, 'uLng' => $uLng, 'tLat' => $tLat, 'tLng' => $tLng, 'a' => $a, 'bBox'=>$bBox, 'form' => $form->createView()));
        
        }
        
    }
}