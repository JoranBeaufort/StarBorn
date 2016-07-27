<?php

namespace JoranBeaufort\Neo4jSpatialBundle\Manager;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GuzzleHttp\Client;


class SpatialManager
{
    private $protocol;
    private $user;
    private $pass;
    private $url;
    private $port;

    public function setConfig( $config )
    {
        $this->protocol = $config['protocol'];
        $this->user = $config['username'];
        $this->pass = $config['password'];
        $this->url  = $config['url'];
        $this->port = $config['port'];
    }
    
    public function getClient()
    {              
        
        $baseUrl = sprintf(
            '%s://%s:%s@%s:%s',
            $this->protocol,
            $this->user,
            $this->pass,
            $this->url,
            $this->port
        );
        

        $client = new Client([
            'base_uri' => $baseUrl,
            'headers' => [
                'Accept' => 'application/json; charset=UTF-8',
            ],
            'auth' => [
                $this->user,
                $this->pass,
            ],
        ]);
              
        return $client;
    }
}
