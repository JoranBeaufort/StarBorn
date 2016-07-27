<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Team;

class MapController extends Controller
{
    public function indexAction()
    {    
        
        $em = $this->get('neo4j.graph_manager')->getClient();

        $user = $this->getUser();
        $red = $em->getRepository(Team::class)->findOneBy('name','red_giants');
        $redId = $red->getId();
        $blue = $em->getRepository(Team::class)->findOneBy('name','blue_dwarfs');
        $blueId = $blue->getId();
       
        
        return $this->render(
            'AppBundle:Map:map.html.twig',array('user' => $user, 'redGiantsId' => $redId, 'blueDwarfsId' => $blueId)
        );
    }
    
    public function getJsonAction($lat,$lng,$a)
    {
       $encoder = $this->get('nzo_url_encryptor');
       
       $a = $encoder->decrypt($a);
       
       if($this->getUser()->getUid() == $a){ 
            // Get all the tiles in the area
            $q=   "SELECT 
                row_to_json(fc) as GeoJSON
            FROM ( 
                SELECT 
                    'FeatureCollection' as type, 
                    array_to_json(array_agg(f)) as features
                FROM (
                    SELECT 'Feature' as type,
                    (ST_AsGeoJSON(ST_Transform(ST_SetSRID((b.UID).geom,2056),4326))::json ) as geometry,
                    (
                    SELECT row_to_json(t) 
                        FROM (select ST_X(b.BBOX_BL) as blx, ST_Y(b.BBOX_BL) as bly, ST_X(b.BBOX_BR) as brx, ST_Y(b.BBOX_BR) as bry, ST_X(b.BBOX_TL) as tlx, ST_Y(b.BBOX_TL) as tly, ST_X(b.BBOX_TR) as trx, ST_Y(b.BBOX_TR) as try, (b.UID).val as UID, (b.TID).val as TID ) t
                    ) as properties
                    FROM (
                        SELECT
                            c.UID as UID,
                            c.TID as TID,
                            ST_Transform(ST_SetSRID(ST_MakePoint(ST_X((c.UID).geom)-99,ST_Y((c.UID).geom)-99),2056),4326) AS BBOX_BL,
                            ST_Transform(ST_SetSRID(ST_MakePoint(ST_X((c.UID).geom)+99,ST_Y((c.UID).geom)-99),2056),4326) AS BBOX_BR,
                            ST_Transform(ST_SetSRID(ST_MakePoint(ST_X((c.UID).geom)-99,ST_Y((c.UID).geom)+99),2056),4326) AS BBOX_TL,
                            ST_Transform(ST_SetSRID(ST_MakePoint(ST_X((c.UID).geom)+99,ST_Y((c.UID).geom)+99),2056),4326) AS BBOX_TR
                        FROM (                        
                            SELECT
                                (ST_PixelAsCentroids(u.r,1,false)) As UID,
                                (ST_PixelAsCentroids(u.r,2,false)) As TID
                            FROM(
                                SELECT ST_Union(rast) as r
                                FROM gameField
                                WHERE ST_Intersects(rast, ST_Buffer(ST_Transform(ST_SetSRID(ST_MakePoint(".$lng.",".$lat.") ,4326),2056), 500))
                            ) as u
                        ) as c
                            WHERE
                                ST_Intersects((c.UID).geom, ST_Buffer(ST_Transform(ST_SetSRID(ST_MakePoint(".$lng.",".$lat.") ,4326),2056), 500))
                            AND ST_Intersects((c.TID).geom, ST_Buffer(ST_Transform(ST_SetSRID(ST_MakePoint(".$lng.",".$lat.") ,4326),2056), 500))

                    ) as b   
                ) as f 
            )  as fc;";
            
            
            $em = $this->getDoctrine()->getManager();
            $connection = $em->getConnection();
            $statement = $connection->prepare($q);
            $statement->execute();
            $results = $statement->fetchAll();
        
            $user = $this->getUser();
            
            $response = new Response($results[0]['geojson']);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
       }else{
           echo "ERROR";
           return false;
       }
        
        // echo $results[0]['geojson'];
    }
}