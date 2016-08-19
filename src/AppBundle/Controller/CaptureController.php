<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Form\CaptureInterfaceType;
use AppBundle\Entity\Tile;
use AppBundle\Entity\Drone;
use AppBundle\Entity\Resources;
use JoranBeaufort\Neo4jUserBundle\Entity\User;

class CaptureController extends Controller
{
    public function indexAction(Request $request)
    {    
        $user = $this->getUser();               
        
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
        
        $bBox = '['.$tblx.','.$tbly.'],['.$ttlx.','.$ttly.'],['.$ttrx.','.$ttry.'],['.$tbrx.','.$tbry.']';
        // var_dump($bBox);die;
        // var_dump($uLat);die;
       
        $a = $encoder->decrypt($request->request->get('a'));
        
        $form = $this->createForm(CaptureInterfaceType::class);
        
        $form->handleRequest($request);
        
        
        if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->get('neo4j.graph_manager')->getClient();
                
                // user coords
                $uLat = $request->request->get('ulat');
                $uLng = $request->request->get('ulng');
                
                // tile centroid
                $tLat = $request->request->get('tlat');
                $tLng = $request->request->get('tlng'); 

                $bBox = $request->request->get('bBox');
                

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

                if(!$results || $results[0]['val'] === '0' || $results[0]['val'] === 0){                    
                    
                    // get potential resources
                    $potentialResources = $form->get('landcover')->getData();
                    $potentialTileResources = array();
                    $landcover = array();
                    
                    $resourceMap = array(
                        'forest' => 'wood',
                        'water' => 'water',
                        'agriculture' => 'food',
                        'urban' => 'work',
                        'snow' => 'water',
                        'mountain' => 'stone',
                        'field' => 'wood',
                        'infrastructure' => 'work',
                    );
                    
                    foreach($potentialResources as $pr){
                        
                        $r = $em->getRepository(Resources::class)->findOneBy('name',$resourceMap[$pr]);
                        array_push($potentialTileResources,$r->getName());
                        array_push($landcover, $pr);
                    }
                    
                    $landcover = join(',', $landcover);
                    
                    $em->clear(); 

                    $user = $em->getRepository(User::class)->findOneById($this->getUser()->getId());

                    $tileResources = array();
                    $newResources = new ArrayCollection();
                   
                   for($i=0; $i<3; $i++){
                        $resourceName = $potentialTileResources[array_rand($potentialTileResources)];
                        array_push($tileResources,$resourceName);                        
                        $urR = $user->getUserResource($resourceName);
                        $currentResourceCount = $urR->getAmount();
                        $urR->setAmount($currentResourceCount+1);
                        $newResources->add($urR->getResource());
                   }
                    

                    $setResources = join(',', $tileResources); 
                    
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
                    
                    // get the tile
                    if($results[0]['val'] != -9999){
                        $tile = $em->getRepository(Tile::class)->findOneBy('tid',$results[0]['val']);
                    }else{
                        $tokenGenerator=$this->get('tile.token_generator');
                        $tid=$tokenGenerator->generateTileToken(9);
                        $tile = new Tile($tid,$results[0]['rid'], $tLat, $tLng, $bBox);
                    }
                    

                    $drone = $em->getRepository(Drone::class)->findOneBy('name','nova_xs');
                    
                    $tile->setTileDrone($drone,$drone->getHp());

                    $user->addUserTile($tile, time(),time(),$setResources, $landcover);                    
                    
                    // $user->addUserTileLost($tile, time());
                    
                    
                    $em->flush();          
                    $em->clear(); 
                  
                    $q=   " UPDATE 
                                gameField 
                            SET 
                                rast = ST_SetValue(rast,1,ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326),2056),".$user->getUint().")
                            WHERE 
                                ST_Intersects(rast, ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326),2056));";
                   
                    $statement = $connection->prepare($q);
                    $statement->execute();
                    
                    $q=   " UPDATE 
                                gameField 
                            SET 
                                rast = ST_SetValue(rast,2,ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326),2056),".$user->getUserTeam()->getTeam()->getTid().")
                            WHERE 
                                ST_Intersects(rast, ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326),2056));";
                   
                    $statement = $connection->prepare($q);
                    $statement->execute();
                    
                    $q=   " UPDATE 
                                gameField 
                            SET 
                                rast = ST_SetValue(rast,3,ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326),2056),".$tile->getTid().")
                            WHERE 
                                ST_Intersects(rast, ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326),2056));";
                   
                    $statement = $connection->prepare($q);
                    $statement->execute();
                    
                    $q=   " INSERT INTO 
                                tileLog ( uid,tid, timestamp, lat, lng, resources)
                            VALUES
                                ('".$user->getUid()."','".$tile->getTid()."','".time()."','".$tLat."','".$tLng."','".$setResources."')";
                    
                    $statement = $connection->prepare($q);
                    $statement->execute();

                    
                    return $this->render('AppBundle:Capture:success.html.twig',array('user' => $user, 'newResources' =>$newResources));
                
                }elseif($results[0]['val'] !== '0' || $results[0]['val'] !== 0){
                    $user = $this->getUser();
                    $tileUser = $em->getRepository(User::class)->findOneById(intval($results[0]['val']));                
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
                $tileUser = $em->getRepository(User::class)->findOneById(intval($results[0]['val']));                
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