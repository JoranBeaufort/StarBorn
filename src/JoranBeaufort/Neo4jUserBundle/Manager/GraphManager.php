<?php

namespace JoranBeaufort\Neo4jUserBundle\Manager;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GraphAware\Neo4j\OGM\EntityManager;


class GraphManager
{
    private $protocol;
    private $user;
    private $pass;
    private $url;
    private $port;

    private $em;

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
        if (null === $this->em) {
            if($this->user=='' || $this->pass == ''){
                $connection =  $this->protocol.'://'.$this->url.':'.$this->port;
            }else{
                $connection =  $this->protocol.'://'.$this->user.':'.$this->pass.'@'.$this->url.':'.$this->port;
            }

            $this->em = EntityManager::create($connection);
        }


        
        return $this->em;
    }
}
