<?php

namespace JoranBeaufort\Neo4jSpatialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class TestController extends Controller
{
    public function indexAction()
    {
     //   echo phpinfo();die;
    $time_start = microtime(true);
    /*$q = '  SELECT pg_database.datname as "Database",
                   pg_user.usename as "Owner"FROM pg_database, pg_user
            WHERE pg_database.datdba = pg_user.usesysid

            UNION

            SELECT pg_database.datname as "Database",
                   NULL as "Owner"FROM pg_database
            WHERE pg_database.datdba NOT IN (SELECT usesysid FROM pg_user)
            ORDER BY "Database"';
    */
    
    // $q ="UPDATE gameField SET rast = ST_AddBand(rast, 1, '32BUI'::text, 0, NULL)";
    // $q='UPDATE gameField SET rast = ST_SetBandNoDataValue(rast, 0);';
    
    // $q='UPDATE myRasterTable SET rast = ST_SetValue(rast, 1,ST_Transform(ST_SetSRID(ST_MakePoint(7.5,48.5),4326),2056),987.654321)';
    // $q='SELECT rid, ST_Value(rast, ST_Transform(ST_SetSRID(ST_MakePoint(7.0,48.5),4326),2056),false) val FROM myRasterTable';
    

    // $q="SELECT ST_Tile(rast, 50,50, TRUE, NULL) FROM myRasterTable";
    // $q = 'TRUNCATE myRasterTable';
    // $q = 'DROP INDEX myrastertable_rast_gist_idx';
    // $q="INSERT INTO gameField(rast) VALUES (ST_Tile(ST_MakeEmptyRaster( 3520, 2220, 2485869.5728, 1299941.7864, 100, 100, 0, 0, 2056), 10,10, TRUE, NULL));";
    // $q ="UPDATE myRasterTable SET rast = ST_AddBand(rast, 2, '32BUI'::text, 22, NULL);";
    // $q=" CREATE INDEX myRasterTable_rast_gist_idx ON myRasterTable USING GIST (ST_ConvexHull(rast));";
    // $q = " SELECT (md).*, (bmd).*  FROM (SELECT ST_Metadata(rast) AS md, ST_BandMetadata(rast) AS bmd FROM myRasterTable LIMIT 2) foo;";
    // $q='UPDATE myRasterTable SET rast = ST_SetValue(rast, 1,ST_Transform(ST_SetSRID(ST_MakePoint(7.5,48.5),4326),2056),987654321) WHERE ST_Intersects(rast, ST_Transform(ST_SetSRID(ST_MakePoint(7.5,48.5),4326),2056));';
    // $q="EXPLAIN ANALYZE SELECT rid, ST_Value(rast, ST_Transform(ST_SetSRID(ST_MakePoint(7.501,48.5),4326),2056), false) FROM myRasterTable WHERE ST_Intersects(rast, ST_Transform(ST_SetSRID(ST_MakePoint(7.501,48.5),4326),2056));";
    // --- $q="SELECT rid, ST_Value(rast, ST_Transform(ST_MakeEnvelope(7.5,48.5, 7.6, 48.6 ,4326),2056)) FROM myRasterTable WHERE ST_Intersects(rast, ST_Transform(ST_MakeEnvelope(7.5,48.5, 7.6, 48.6 ,4326),2056));";
    //  $q="SELECT ST_AsGeoJSON(ST_Centroid(ST_Transform((geom.user).geom,4326))), (geom.user).val as user, (geom.building).val as building FROM (SELECT ST_Intersection(ST_Transform(ST_MakeEnvelope(7.5,48.5, 8, 49 ,4326),2056), rast, 1) as user, ST_Intersection(ST_Transform(ST_MakeEnvelope(7.5,48.5, 8, 49 ,4326),2056), rast, 2) as building FROM myRasterTable WHERE ST_Intersects(rast, ST_Transform(ST_MakeEnvelope(7.5,48.5, 8, 49 ,4326),2056))) as geom;";
    // $q = 'INSERT INTO gameField(rid,rast) VALUES(1, ST_MakeEmptyRaster( 3700, 2400, 2483800, 1074400, 100, 100, 0, 0, 2056) );';
    
        // $q ="  INSERT INTO 
    //            gameField(rast) 
    //        VALUES(
    //            ST_Tile(
    //                ST_SetBandNoDataValue(
    //                    ST_AddBand(
    //                        ST_MakeEmptyRaster( 3700, 2400, 2483800, 1074400, 100, 100, 0, 0, 2056)
    //                    , 1, '32BUI'::text, 0, NULL)
    //                , 0)
    //            , 10,10, TRUE, NULL)
    //        );";

    
    /********************************************************************/
    /*
    /*                                  
    /*                      vvv    IN USE    vvv
    /*
    /********************************************************************/
    // $q= "DROP TABLE gameField;";
    // $q='CREATE TABLE gameField (rid SERIAL PRIMARY KEY, rast raster);';
    //$q ="  INSERT INTO 
    //           gameField(rast) 
    //       VALUES(
    //           ST_Tile(
    //                ST_AddBand(
    //                    ST_MakeEmptyRaster( 3700, 2400, 2483800, 1301000, 100, -100, 0, 0, 2056)
    //                , 1, '32BUI'::text, 0, NULL)
    //           , 10,10, TRUE, NULL)
    //       );";
    // $q=" CREATE INDEX gameField_rast_gist_idx ON gameField USING GIST (ST_ConvexHull(rast));";
    
    // $q ="UPDATE gameField SET rast = ST_AddBand(rast, 2, '32BUI'::text, 0, NULL)";
    // $q='UPDATE gameField SET rast = ST_SetBandNoDataValue(rast, 2, NULL);';
    
    // $q='UPDATE gameField SET rast = ST_SetValue(rast, 1, ST_Transform(ST_SetSRID(ST_MakePoint(6.25,45.25),4326),2056),987654321) WHERE ST_Intersects(rast, ST_Transform(ST_SetSRID(ST_MakePoint(6.25,45.25),4326),2056));';
    // $q ="SELECT rid, ST_Value(rast, ST_Transform(ST_SetSRID(ST_MakePoint(6.25,45.25),4326),2056) , false) FROM gameField WHERE ST_Intersects(rast, ST_Transform(ST_SetSRID(ST_MakePoint(6.25,45.25),4326),2056));";
    
    // $q='WITH vars as(SELECT ST_Transform(ST_SetSRID(ST_MakePoint(7.26115,47.13153),4326),2056) as coord) UPDATE gameField SET rast = ST_SetValue(rast, 2, vars.coord ,23) FROM vars WHERE ST_Intersects(rast, vars.coord);';
    // $q ="SELECT rid, ST_Value(rast, coord) FROM gameField, ST_Transform(ST_SetSRID(ST_MakePoint(6.005,46.005),4326),2056) as coord WHERE ST_Intersects(rast,coord)";
    // $q="SELECT ST_AsText(ST_Transform(ST_SetSRID(ST_MakePoint(2851800  ,1061000),2056),4326)) as TRANS";
    
    
    // $q = 'SELECT rid, ST_Summary(rast) As md FROM gameField where rid =88799;'; // 88799';
    // $q= 'select count(*) from gameField;';

     
 $q=   "SELECT 
            row_to_json(fc) as GeoJSON
        FROM ( 
            SELECT 
                'FeatureCollection' as type, 
                array_to_json(array_agg(f)) as features
            FROM (
                SELECT 'Feature' as type,
                (ST_AsGeoJSON(ST_Transform(ST_SetSRID((c.UID).geom,2056),4326))::json ) as geometry,
                (
                SELECT row_to_json(t) 
                    FROM (select (c.UID).val as UID, (c.BID).val as BID ) t
                ) as properties
                FROM (
                    SELECT
                        (ST_PixelAsCentroids(u.r,1,false)) As UID,
                        (ST_PixelAsCentroids(u.r,2,false)) As BID                    
                    FROM(
                        SELECT ST_Union(rast) as r
                        FROM gameField
                        WHERE ST_Intersects(rast, ST_Transform(ST_MakeEnvelope(7.26, 47.13, 7.263, 47.132 ,4326),2056))
                    ) as u
                ) as c   
            ) as f 
        )  as fc;";
          
   // $q ="   SELECT val, ST_AsText(ST_Transform(ST_SetSRID((geom),2056),4326)) FROM (   
   //             SELECT
   //                 (ST_PixelAsCentroids(u.r,1,false)).* As UID,
   //                 (ST_PixelAsCentroids(u.r,1,false)).* As BID,                    
   //             FROM(
   //                 SELECT ST_Union(rast) as r
   //                 FROM gameField
   //                 WHERE ST_Intersects(rast, ST_Transform(ST_MakeEnvelope(7.26, 47.13, 7.27, 47.14 ,4326),2056))
   //             ) as u
   //         ) as c;";
            
    
        
    $em = $this->getDoctrine()->getManager();
    $connection = $em->getConnection();
    $statement = $connection->prepare($q);
    $statement->execute();
    $results = $statement->fetchAll();
   // print_r($results);
    
    $time_end = microtime(true);
    
    /*
    $em = ClientBuilder::create()
    ->addConnection('bolt', 'bolt://neo4j:WQs3;40.eS@localhost:7687') // Example for BOLT connection configuration (port is optional)
    ->build();
    
    $time_start = microtime(true);
    $em = $this->get('neo4j.graph_manager')->getClient();
   
   /*
    for($i=0; $i<317; $i++){
        $stack = $em->stack();
        for($j=0; $j<317; $j++){
            $lat = 46+($i/100);
            $lng = 6+($j/100);
            
            $stack->push('create (n:Tile{name:"'.$i*$j.'",latitude:'.$lat.',longitude:'.$lng.'}) with n call spatial.addNode("geom",n) YIELD node return false');
        }
        $results = $em->runStack($stack);
        echo 'stack nr: '.$i.' executed!';
    }
    /*

        // define entity manager
        $client = $this->get('neo4j.spatial_manager')->getClient();
        
        $time_start = microtime(true); 
            $request = $client->request('POST',
                '/db/data/ext/SpatialPlugin/graphdb/findGeometriesWithinDistance',
                [
                    'json' => [
                        'layer' => 'geom',
                        'pointY' => 47,
                        'pointX' => 7,
                        'distanceInKm' => 1,
                    ],
                ]
            ); 
        $time_end = microtime(true);
        $data = json_decode($request->getBody(), true);
        echo "Nodes:".count($data).'<br>';
        //var_dump($data);
        
        
        
        /*
        
        // 1. Create a pointlayer
        $request = $client->request('POST',
            '/db/data/ext/SpatialPlugin/graphdb/addSimplePointLayer',
            [
                'json' => [
                    'layer' => 'geom',
                    'lat' => 'lat',
                    'lon' => 'lon',
                ],
            ]
        );
        
        var_dump($request);
       
        
        // 2. Create a spatial index
        $request = $client->request('POST',
            '/db/data/index/node/',
            [
                'json' => [
                    'name' => 'geom',
                    'config' => [
                        'provider' => 'spatial',
                        'geometry_type' => 'point',
                        'lat' => 'lat',
                        'lon' => 'lon',
                    ],
                ],
            ]
        );
        
        var_dump($request);
        *//*
        
        // 3. Create a node (or nodes) with spatial data
        $time_start = microtime(true);
        for($i=0; $i<317; $i++){
            for($j=0; $j<317; $j++){
                $lat = 46+($i/100);
                $lng = 6+($j/100);
                $json = [
                    'val' => $i*$j,
                    'name' => 'TEST',
                    'latitude' => $lat,
                    'longitude' => $lng,
                ];
                $response = $client->request('POST',
                    '/db/data/node', 
                    [
                        'json' => $json
                    ]
                );
                
                $node = json_decode($response->getBody(), true)['self'];

                // 4. Add a node (or nodes) to the spatial index
                $response = $client->request('POST',
                    '/db/data/ext/SpatialPlugin/graphdb/addNodeToLayer',
                    [
                        'json' => [
                            'layer' => 'geom',
                            'node' => $node,
                        ],
                    ]
                );
            }
        }
        */
        
        echo $time_end-$time_start;
        echo '  Seconds';
        
       // $data = json_decode($response->getBody(), true);

       // var_dump($data);
        
        /**/                

        return $this->render('Neo4jSpatialBundle:Default:index.html.twig', array('geojsonFeature' =>  $results[0]['geojson']));
    }
}
