<?php


namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Tile;


class TileController extends Controller
{
    public function indexAction(Request $request)
    {
        // $post = $request->request->all();
        // var_dump($post);die;

        $user = $this->getUser();
        $encoder = $this->get('nzo_url_encryptor');

        $a = $encoder->decrypt($request->request->get('a'));
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

        // 
        $links = array('capture' => false);

        if($uLat > $tbly  && $uLat < $ttry && $uLng > $tblx  && $uLng < $ttrx){
            $links  = array('capture' => true);
        }

        $a = $encoder->decrypt($request->request->get('a'));

        // Get the node ID
        $em_pgsql = $this->getDoctrine()->getManager();
        $connection = $em_pgsql->getConnection();
        $q=   " SELECT 
                    rid, 
                    ST_Value(rast, 3, ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat."),4326),2056),false) val 
                FROM 
                    gameField 
                WHERE
                    ST_Intersects(rast, 3, ST_Transform(ST_SetSRID(ST_MakePoint(".$tLng.",".$tLat.") ,4326),2056))";

        $statement = $connection->prepare($q);
        $statement->execute();
        $results = $statement->fetchAll();

        $em = $this->get('neo4j.graph_manager')->getClient();

        $tile = $em->getRepository(Tile::class)->findOneBy('tid', $results[0]['val']);

        if($tile != null){


            $structures = array();
            $attackable = array('drone'=>null, 'building'=>null, 'shield'=>null);

            foreach($tile->getTileStructures() as $ts){
                if($ts->getStructure()->getStructureType() == 'drone'){
                    array_push($structures, $ts);
                    $attackable['drone'] = false;

                }
                if($ts->getStructure()->getStructureType() == 'building'){
                    array_push($structures, $ts);
                    $attackable['building'] = false;
                }
                if($ts->getStructure()->getStructureType() == 'shield'){
                    array_push($structures, $ts);
                    $attackable['shield'] = false;
                }
            }

            if($attackable['drone'] === false &&  $attackable['building'] === false && $attackable['shield'] === false){
                $attackable['shield'] = true;
            }elseif($attackable['drone'] === false &&  $attackable['building'] === null && $attackable['shield'] === false){
                $attackable['shield'] = true;
            }elseif($attackable['drone'] === false &&  $attackable['building'] === false && $attackable['shield'] === null){
                $attackable['building'] = true;
            }elseif($attackable['drone'] === false &&  $attackable['building'] === null && $attackable['shield'] === null){
                $attackable['drone'] = true;
            }
            return $this->render('AppBundle:Tile:info.html.twig',array('lat' => $tLat, 'lng' => $tLng, 'a' => $a, 'tile' => $tile, 'structures' => $structures,'attackable' => $attackable, 'links' => $links, 'user' => $user));
        }
        return $this->render('AppBundle:Tile:info.html.twig',array('lat' => $tLat, 'lng' => $tLng, 'a' => $a, 'tile' => $tile, 'links' => $links, 'user' => $user));
    }
}