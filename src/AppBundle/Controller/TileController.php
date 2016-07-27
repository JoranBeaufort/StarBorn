<?php


namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;

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
            
        return $this->render('AppBundle:Tile:info.html.twig',array('lat' => $tLat, 'lng' => $tLng, 'a' => $a, 'links' => $links));
    }
}