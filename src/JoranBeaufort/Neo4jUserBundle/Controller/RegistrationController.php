<?php
namespace JoranBeaufort\Neo4jUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;


use JoranBeaufort\Neo4jUserBundle\Form\UserType;
use JoranBeaufort\Neo4jUserBundle\Entity\User;
use JoranBeaufort\Neo4jUserBundle\Entity\Role;
use JoranBeaufort\Neo4jUserBundle\Entity\UserRole;
use AppBundle\Entity\Resources;
use AppBundle\Entity\Inventory;
use AppBundle\Entity\UserResource;

class RegistrationController extends Controller
{
    
    private function encodePassword(User $user, $plainPassword)
    {
        $encoder = $this->container->get('security.encoder_factory')
            ->getEncoder($user);

        return $encoder->encodePassword($plainPassword, $user->getSalt());
    }
    
    public function registerAction(Request $request)
    {
        // define entity manager
        $em = $this->get('neo4j.graph_manager')->getClient();
        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUsernameCanonical(mb_convert_case($user->getUsername(), MB_CASE_LOWER, "UTF-8"));
            $user->setEmailCanonical(mb_convert_case($user->getEmail(), MB_CASE_LOWER, "UTF-8"));
            $uniqueScreenname=$em->getRepository(User::class)->findOneBy('screenname', $user->getScreenname());
            $uniqueUsername=$em->getRepository(User::class)->findOneBy('usernameCanonical', $user->getUsernameCanonical());
            $uniqueEmail=$em->getRepository(User::class)->findOneBy('emailCanonical', $user->getEmailCanonical());
            
           

            $errors=FALSE;
            
            if ($uniqueUsername) {
                // Check for uniqueness
                $error = new FormError("Dieser Benutzername ist schon vergeben.");
                 
                $form->get('username')->addError($error);
               
                $errors=TRUE;
            }

            if ($uniqueScreenname) {
                // Check for uniqueness
                $error = new FormError("Dieser Anzeigenamen ist schon vergeben.");

                $form->get('screenname')->addError($error);

                $errors=TRUE;
            }

            if ($uniqueEmail) {
                // Check for uniqueness
                $error = new FormError("Diese Emailadresse wird schon verwendet!");
                $form->get('email')->addError($error);
                $errors=TRUE;
            }

             
            if($errors===FALSE){
                
                $tokenGenerator=$this->get('neo4j.token_generator');
                $confirmationToken=$tokenGenerator->generateConfirmationToken(24);
                $uint=$tokenGenerator->generateUserIntToken(9);
                $uid=$tokenGenerator->generateUserToken(8);
                $password=$this->get('security.password_encoder')->encodePassword($user, $user->getPlainPassword());
                $dateTime = new \dateTime;
                $dateTime = $dateTime->format('Y-m-d H:i:s');                
        
                $user->setPassword($password);
                $user->setConfirmationToken($confirmationToken);
                $user->setIsEnabled(false);
                $user->setIsAccountNonExpired(true);
                $user->setIsAccountNonLocked(true);
                $user->setIsCredentialsNonExpired(true);
                $user->setUint($uint);
                $user->setUid($uid);
                $user->setXP(0);
                $user->setRegistrationDateTime($dateTime);                  
                
                $inventory = new Inventory(20);
                $user->addInventory($inventory);
                $em->persist($inventory);
                
                // Add initial role
                $role=$em->getRepository(Role::class)->findOneBy('roleType', 'ROLE_USER'); 
                $user->addRole($role);
                
                
                $em->persist($user);
                $em->flush();

                $url = $this->generateUrl('neo4j_register_check_email');
                
                /* --------__ Send Email with Token __----------- */
                 $message = \Swift_Message::newInstance()
                    ->setSubject($this->getParameter('neo4j_user.mail.subject.registration'))
                    ->setFrom($this->getParameter('mailer_user'))
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            // app/Resources/views/Emails/registration.html.twig
                            'Neo4jUserBundle:Registration:email.html.twig',
                            array('username' => $user->getUsername(), 'confirmationToken' => $user->getConfirmationToken())
                        ),
                        'text/html'
                    );
            $this->get('mailer')->send($message); 
            
                
            return new RedirectResponse($url);
            }
        }
        
        return $this->render('Neo4jUserBundle:Registration:register_content.html.twig',array('form' => $form->createView()));
    
    }
    
    public function checkEmailAction()
    {
        return $this->render('Neo4jUserBundle:Registration:checkEmail.html.twig');
    }
    
     public function confirmedAction($token)
    {
        $error = false;
        $em = $this->get('neo4j.graph_manager')->getClient();        
        $user=$em->getRepository(User::class)->findOneBy('confirmationToken', $token);

        
        if (!$user) {
            $error = true;
            $message="<br><br>Kein User mit dem Bestätigungscode:<b> ".$token." </b>wurde gefunden<br><br><br>";
            return $this->render('Neo4jUserBundle:Registration:confirmed.html.twig', array('error' => $error, 'message' => $message));
        }else{
            

           
            // Add initial resources
            $resource=$em->getRepository(Resources::class)->findOneBy('name', 'ethertoken');
            $user->addResource($resource, 1);
            
            $resource=$em->getRepository(Resources::class)->findOneBy('name', 'stardust');
            $user->addResource($resource, 50);


            $user->setConfirmationToken(null);
            $user->setIsEnabled(true);
            
            // var_dump($user->getUserResources());die;
            // 4) save the User!
            $em->persist($user);
            $em->flush();
            $message = null;
        }
        return $this->render('Neo4jUserBundle:Registration:confirmed.html.twig', array('error' => $error, 'message' => $message));
    }

}