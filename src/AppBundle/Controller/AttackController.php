<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use AppBundle\Entity\Tile;
use JoranBeaufort\Neo4jUserBundle\Entity\User;


class AttackController extends Controller
{
    public function indexAction(Request $request)
    {            
        $encoder = $this->get('nzo_url_encryptor');
        
        // user coords
        $uLat = $request->request->get('ulat');
        $uLng = $request->request->get('ulng');
        
        // tile centroid
        $t = $request->request->get('t');
        $w = $request->request->get('w');
        $tid = $request->request->get('tid');
                
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
        $team = $statement->fetchAll();

        $q=   " SELECT 
                    rid, 
                    ST_Value(rast, 1, ST_Transform(ST_SetSRID(ST_MakePoint(".$uLng.",".$uLat."),4326),2056),false) val 
                FROM 
                    gameField 
                WHERE
                    ST_Intersects(rast, 1, ST_Transform(ST_SetSRID(ST_MakePoint(".$uLng.",".$uLat.") ,4326),2056))";

        $statement = $connection->prepare($q);
        $statement->execute();
        $uint = intval($statement->fetchAll()[0]['val']);


        $em = $this->get('neo4j.graph_manager')->getClient();
        /* @var $user \JoranBeaufort\Neo4jUserBundle\Entity\User */
        $user = $em->getRepository(User::class)->findOneBy('uid',$this->getUser()->getUid());

        if($team[0]['val'] == $tid){
            $fb = $this->get('session')->getFlashBag();

            if($w === 'primary'){
                $cd = time()-($user->getPrimary()+10);
                if($cd>=0) {
                    // $dmg = 20;
                    $dmg = 40;
                    if(intval($user->getLvl()) < 8){
                        $dmg = $dmg*1.5;
                    }
                }
            }elseif($w === 'secondary'){
                $cd = time()-($user->getSecondary()+180);
                if($cd>=0) {
                    // $dmg = 100;
                    $dmg = 200;
                    if(intval($user->getLvl()) < 8){
                        $dmg = $dmg*1.5;
                    }
                }
            }else{
                $dmg = 0;
            }

            if ($cd >= 0) {
                $nr = $em->getDatabaseDriver()->run("match (t:Tile{tid:'" . $tid . "'})-[hs:HAS_STRUCTURE]->(s:Structure{type:'" . $t . "'}), (u:User{uid:'" . $this->getUser()->getUid() . "'}) set u.".$w." = ".time().", u.xp=(u.xp+1), hs.hp=(TOINT(hs.hp)-TOINT(".$dmg.")) return hs.hp as hp, s.img as img, t.tLat as lat, t.tLng as lng");

                if($nr->firstRecord()->get('hp')<=0){

                    if ($t == 'drone') {
                        $q="MATCH(t:Tile{tid:'" . $tid . "'})-[hs:HAS_STRUCTURE]->(s:Structure{type:'drone'}), (u:User)-[c:CAPTURED]->(t), (u2:User{uid:'" . $this->getUser()->getUid() . "'}) WHERE TOINT(u.uint)=TOINT('".$uint."') DELETE hs SET u2.xp=(u2.xp+4), c.lost = " . time() . " WITH c call apoc.refactor.setType(c, 'LOST') yield input, output return false";
                        $em->getDatabaseDriver()->run($q);
                        $lat = $nr->firstRecord()->get('lat');
                        $lng = $nr->firstRecord()->get('lng');
                        $q = " UPDATE 
                                    gameField 
                                SET 
                                    rast = ST_SetValue(rast,2,ST_Transform(ST_SetSRID(ST_MakePoint(" . $lng . "," . $lat . "),4326),2056),0)
                                WHERE 
                                    ST_Intersects(rast, ST_Transform(ST_SetSRID(ST_MakePoint(" . $lng . "," . $lat . "),4326),2056));";

                        $statement = $connection->prepare($q);
                        $statement->execute();

                        $q = " UPDATE 
                                    gameField 
                                SET 
                                    rast = ST_SetValue(rast,1,ST_Transform(ST_SetSRID(ST_MakePoint(" . $lng . "," . $lat . "),4326),2056),0)
                                WHERE 
                                    ST_Intersects(rast, ST_Transform(ST_SetSRID(ST_MakePoint(" . $lng . "," . $lat . "),4326),2056));";

                        $statement = $connection->prepare($q);
                        $statement->execute();

                        $url = $this->generateUrl('map');
                        return new RedirectResponse($url);
                    }else{
                        $em->getDatabaseDriver()->run("match (t:Tile{tid:'" . $tid . "'})-[hs:HAS_STRUCTURE]->(s:Structure{type:'" . $t . "'}), (u:User{uid:'" . $this->getUser()->getUid() . "'}) set u.xp=(u.xp+4) delete hs");
                    }
                }
                $fb->add('success', true);
                $fb->add('success-message', $dmg . ' Schaden verursacht!');
                $fb->add('success-img', $nr->firstRecord()->get('img'));
            }
        }else{
             throw new \Exception('IDs dont match. Uiuiui!');
        }
        $em->clear();
        // $user = $em->getRepository(User::class)->findOneBy('uid',$this->getUser()->getUid());
        return $this->forward('AppBundle:Scan:index');
    }
}