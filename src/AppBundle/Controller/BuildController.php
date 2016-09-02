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
use AppBundle\Entity\Inventory;
use AppBundle\Entity\UserResource;
use JoranBeaufort\Neo4jUserBundle\Entity\User;

class BuildController extends Controller
{
    public function indexAction(Request $request)
    {    
        
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
        // var_dump($results);die;
        $em = $this->get('neo4j.graph_manager')->getClient();
        $tile = $em->getRepository(Tile::class)->findOneBy('tid',$results[0]['val']);

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
        
        $message = null;
        
        // $test = $user->getUserInventory()->getInventory()->getBlueprintInventoriesByType('building');
        // foreach($test as $t){
        //     var_dump($t->getBlueprint()->getName());
        // }die;
        $user = $em->getRepository(User::class)->findOneBy('uid',$this->getUser()->getUid());
        $em->clear();

        return $this->render('AppBundle:Build:build.html.twig',array('uLat' => $uLat, 'uLng' => $uLng, 'a' => $a, 'user' => $user, 'tile' => $tile, 'structures' => $structures, 'buildable' => $buildable, 'message' => $message));
        
    }
}