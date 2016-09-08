<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Entity\Blueprint;
use JoranBeaufort\Neo4jUserBundle\Entity\User;



class BuyController extends Controller
{
    public function indexAction(Request $request)
    {
        /**
         * @var $blueprint \AppBundle\Entity\Blueprint
         * @var $user \JoranBeaufort\Neo4jUserBundle\Entity\User
         */

        $encoder = $this->get('nzo_url_encryptor');

        // tile centroid
        $amount = $request->request->get('amount');
        $bid = $request->request->get('bid');
        $uid = $encoder->decrypt($request->request->get('a'));

        $em = $this->get('neo4j.graph_manager')->getClient();
        if ($uid == $this->getUser()->getUid()) {
            $user = $em->getRepository(User::class)->findOneBy('uid', $this->getUser()->getUid());
            $blueprint = $em->getRepository(Blueprint::class)->findOneBy('bid', intval($bid));


            if ($user->getUserInventory()->getInventory()->getBlueprintInventoryByBid($bid) != null) {
                // save new amount
                $bi = $user->getUserInventory()->getInventory()->getBlueprintInventoryByBid($bid);
                $biAmount = $bi->getAmount();
                $bi->setAmount($biAmount + $amount);

            } else {
                $user->getUserInventory()->getInventory()->addBlueprintInventory($blueprint, $amount);
            }

            // save new resource amounts of user
            foreach ($blueprint->getBlueprintResources() as $resource) {
                $r = $user->getUserResource($resource->getResources()->getName());
                $rAmount = $r->getAmount();
                $r->setAmount($rAmount - ($resource->getAmount() * $amount));
            }
            $em->flush();
            $em->clear();

            // set flash messages
            $fb = $this->get('session')->getFlashBag();
            $fb->add('success', true);
            $fb->add('success-message', $blueprint->getName_DE() . ' wurde erfolgreich gekauft!');
            $fb->add('success-img','/img/toy-576520.svg');

            $blueprints = $em->getRepository(Blueprint::class)->findAll();

            return $this->render('AppBundle:Store:store.html.twig', array('user' => $user, 'blueprints' => $blueprints));
        } else {
            throw new \Exception('IDs dont match. Uiuiui!');
        }
    }
}
