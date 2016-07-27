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
use AppBundle\Entity\Team;
use AppBundle\Entity\Resources;
use AppBundle\Entity\UserResource;
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
        $ttrx = $request->request->get('ttrx');
        $ttry = $request->request->get('ttry');
        
        //var_dump($uLat);die;
       
        $a = $encoder->decrypt($request->request->get('a'));
        
        $form = $this->createForm(CaptureInterfaceType::class);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('neo4j.graph_manager')->getClient();
            $user = $em->getRepository(User::class)->findOneById($this->getUser()->getId());
            
            // user coords
            $uLat = $request->request->get('ulat');
            $uLng = $request->request->get('ulng');
            
            // tile centroid
            $tLat = $request->request->get('tlat');
            $tLng = $request->request->get('tlng');        
            

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
            var_dump($results);die;
            if(!$results || $results[0]['val'] === '0' || $results[0]['val'] === 0){
                
                $potentialResources = $form->get('landcover')->getData();
                $potentialTileResources = array();
                foreach($potentialResources as $pr){
                    $r = $em->getRepository(Resources::class)->findOneBy('resourceType',$pr);
                    array_push($potentialTileResources,$r->getResourceType());
                }
                
                $tileResources = array();
                for($i=0; $i<3; $i++){
                    $resourceName = $potentialTileResources[array_rand($potentialTileResources)];
                    array_push($tileResources,$resourceName);
                    $urR = $user->getUserResource($resourceName);
                    $currentResourceCount = $urR->getAmount();
                    $urR->setAmount($currentResourceCount+1);
                }
                
                $setResources = join(',', $tileResources); 
                                
                $tile = new Tile($results[0]['rid'], $tLat, $tLng); 
               
                $tile->setResources($setResources); 
                            
                
                $em->persist($tile);

                $user->addTile($tile, time(),time());
                // print_r($user->getUserTiles());die;
                $em->persist($user);
                $em->flush();      
                                
                $q=   " UPDATE 
                            gameField 
                        SET 
                            rast = ST_SetValue(rast,1,ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326),2056),".$user->getId().")
                        WHERE 
                            ST_Intersects(rast, ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326),2056));";
            
                $statement = $connection->prepare($q);
                $statement->execute();
                
                $q=   " UPDATE 
                            gameField 
                        SET 
                            rast = ST_SetValue(rast,2,ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326),2056),".$user->getUserTeam()->getTeam()->getId().")
                        WHERE 
                            ST_Intersects(rast, ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326),2056));";
            
                $statement = $connection->prepare($q);
                $statement->execute();
                
            }elseif($results[0]['val'] !== '0' || $results[0]['val'] !== 0){
                
            }
            
            return $this->render('AppBundle:Dashboard:index.html.twig',array('user' => $user));
        }else{        
            return $this->render('AppBundle:Capture:capture.html.twig',array('uLat' => $uLat, 'uLng' => $uLng, 'tLat' => $tLat, 'tLng' => $tLng, 'a' => $a, 'form' => $form->createView()));
        }
    }
}