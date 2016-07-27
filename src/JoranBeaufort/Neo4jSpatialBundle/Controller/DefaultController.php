<?php

namespace JoranBeaufort\Neo4jSpatialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GuzzleHttp\Exception\RequestException;

class TestController extends Controller
{
    public function indexAction()
    {
        // define entity manager
        $em = $this->get('neo4j.spatial_manager')->getClient();
        
         // 1. Create a pointlayer
        $response = $em->post(
            '/data/ext/SpatialPlugin/graphdb/addSimplePointLayer/',
            [
                'json' => [
                    'layer' => 'geom',
                    'lat' => 'lat',
                    'lon' => 'lon',
                ],
            ]
        );
        
        d($response->json());
        
        catch (RequestException $e) {
            d($e->getRequest());
            if ($e->hasResponse()) {
                d($e->getResponse());
                d($e->getResponse()->json());
            }
        } 
        catch (\Exception $e) {
            d($e->getMessage());
        }
        
        echo "spatial index created";
        die;
        
        return $this->render('Neo4jSpatialBundle:Default:index.html.twig');
    }
}
