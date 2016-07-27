<?php
namespace JoranBeaufort\Neo4jUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

use JoranBeaufort\Neo4jUserBundle\Form\UserType;
use JoranBeaufort\Neo4jUserBundle\Entity\User;
use JoranBeaufort\Neo4jUserBundle\Entity\Role;
use AppBundle\Entity\Resources;
use AppBundle\Entity\UserResource;

class TestController extends Controller
{
    
     public function testAction($token)
    { 

        $usernameInput = 'test';
        $resourceInput1 = 'stone';
        $resourceInput2 = 'water';
        $resourceInput3 = 'food';
        
        $em = $this->get('neo4j.graph_manager')->getClient();
        
        echo 'Loading user with token: '.$token.'<br>';        
        $user=$em->getRepository(User::class)->findOneBy('confirmationToken', $token);
        if($user->getConfirmationToken() == $token){
            echo '<p style="color:green">OK</p><br>';
        }else{
            echo '<p style="color:red">NOT OK</p><br>';
        }
        
        echo 'Get roles of user:<br>';        
        if($user->getRoles()){
            print_r($user->getRoles());
            echo '<p style="color:green">OK</p><br>';
        }else{
            echo '<p style="color:red">NOT OK</p><br>';
        }
        
        echo 'Loading resource with type: '.$resourceInput1.'<br>';        
        $resource=$em->getRepository(Resources::class)->findOneBy('resourceType', $resourceInput1);    
	    $em->persist($resource);        
        if($resource->getResourceType() == $resourceInput1){
            echo '<p style="color:green">OK</p><br>';
        }else{
            echo '<p style="color:red">NOT OK</p><br>';
        }
        
        echo 'Adding Resource to User<br>';        
        $user->addResource($resource, 15);
        echo '<p style="color:green">OK</p><br>';

        
        echo 'Loading resource with type: '.$resourceInput2.'<br>';        
        $resource=$em->getRepository(Resources::class)->findOneBy('resourceType', $resourceInput2);  
	    $em->persist($resource);        
        if($resource->getResourceType() == $resourceInput2){
            echo '<p style="color:green">OK</p><br>';
        }else{
            echo '<p style="color:red">NOT OK</p><br>';
        }
        
        echo 'Adding Resource to User<br>';        
        $user->addResource($resource, 10);
        echo '<p style="color:green">OK</p><br>';

        echo 'Loading resource with type: '.$resourceInput3.'<br>';        
        $resource=$em->getRepository(Resources::class)->findOneBy('resourceType', $resourceInput3); 
	    $em->persist($resource);        
        if($resource->getResourceType() == $resourceInput3){
            echo '<p style="color:green">OK</p><br>';
        }else{
            echo '<p style="color:red">NOT OK</p><br>';
        }
        
        echo 'Adding Resource to User<br>';        
        $user->addResource($resource, 5);
        echo '<p style="color:green">OK</p><br>';
        
        $user->setConfirmationToken(null);
        $user->setIsEnabled(true);
            
        echo 'Persisting the User<br>';        
        $em->persist($user);
        echo '<p style="color:green">OK</p><br>';
        
        echo 'Flush<br>';        
        $em->flush();
        echo '<p style="color:green">OK</p><br>';
        
        echo 'Clear EM<br>';        
        $em->clear();
        echo '<p style="color:green">OK</p><br>';
        
        
	echo 'Reloading user with Username: '.$usernameInput.'<br>';        
        $user=$em->getRepository(User::class)->findOneBy('username', $usernameInput);
        if($user->getUsername() == $usernameInput){
            echo '<p style="color:green">OK</p><br>';
        }else{
            echo '<p style="color:red">NOT OK</p><br>';
        }
        
        echo 'Checking is user has attached resource of type <b>'.$resourceInput.'</b><br>';        
	echo $usernameInput . PHP_EOL;
	foreach ($user->getUserResources() as $userResource) { echo $userResource->getResource()->getName_DE() . PHP_EOL; }
        if($user->getUserResource($resourceInput)){
            print($user->getUserResource($resourceInput)->getResourceType());            
            echo '<p style="color:green">OK</p><br>';
        }else{
            echo '<p style="color:red">NOT OK</p><br>';
        }
        
        
        echo 'Check if role is connected and still has properties<br>'; 
        if($user->getRoles()){ 
            print_r($user->getRoles());
            echo '<p style="color:green">OK</p><br>';
        }else{
            echo '<p style="color:red">NOT OK</p><br>';
        }
        
        // Recreate original state in DB:
       // $em->getDatabaseDriver()->run("match (n)-[r]-() where not n:Resources and not n:Team and not n:User delete r,n");
       // $em->getDatabaseDriver()->run("match (n:User{username:'test'}) create (m:Role{roleType:'ROLE_USER'}), (n)-[r:HAS_ROLE]->(m)");
        
         die;
        return false;
    }
    
     public function test2Action()
    { 

        $em = $this->get('neo4j.graph_manager')->getClient();
        $testUser = 'test';
        echo 'Loading user with name: '.$testUser.'<br>';        
        $user=$em->getRepository(User::class)->findOneBy('username', $testUser);
        if($user->getUsername() == $testUser){
            echo '<p style="color:green">OK</p><br>';
        }else{
            echo '<p style="color:red">NOT OK</p><br>';
        }
        
        echo 'Get roles of user:<br>';        
        if($user->getRoles()){
            print_r($user->getRoles());
            echo '<p style="color:green">OK</p><br>';
        }else{
            echo '<p style="color:red">NOT OK</p><br>';
        }
        
       $r = $em->getRepository(Resources::class)->findOneBy('resourceType','stone');
       
        
        echo 'Get resources of user:<br>';        
        if($user->getUserResources()){
            if(count($user->getUserResources()) == 6){
                foreach($user->getUserResources() as $resource){
                    echo "<i>".$resource->getResource()->getResourceType()." : ".$resource->getAmount()."</i><br>";
                }
                echo '<p style="color:green">OK</p><br>';
            }elseif(count($user->getUserResources()) != 6){
                foreach($user->getUserResources() as $resource){
                    echo "<i>".$resource->getResource()->getResourceType()." : ".$resource->getAmount()."</i><br>";
                }
                echo '<p style="color:red">NOT OK - There should be 6 resources but only found '.count($user->getUserResources()).'</p><br>';
            }
        }else{
            echo '<p style="color:red">NOT OK</p><br>';
        }


        echo 'Clear EM<br>';        
        $em->clear();
        echo '<p style="color:green">OK</p><br>';        
        
        
       // DROP ALL DATA IN GRAPH !!!CAREFULL!!!:
       // $em->getDatabaseDriver()->run("match (n)-[r]-() delete r,n");
       // $em->getDatabaseDriver()->run("match (n) delete n");
       
       // QUERY TO CREATE TEST DATA IN DB
       // $em->getDatabaseDriver()->run("create (a:Resources{resourceType:'wood',name_DE:'Holz',icon:'fa-tree',iconColour:'#fff',colour:'#00C851'}) create (b:Resources{resourceType:'stone',name_DE:'Stein',icon:'fa-cubes',iconColour:'#fff',colour:'#a1887f'}) create (c:Resources{resourceType:'food',name_DE:'Nahrung',icon:'fa-cutlery',iconColour:'#fff',colour:'#fb8c00'}) create (d:Resources{resourceType:'water',name_DE:'Wasser',icon:'fa-tint',iconColour:'#fff',colour:'#33b5e5'}) create (e:Resources{resourceType:'work',name_DE:'Arbeitskraft',icon:'fa-industry',iconColour:'#fff',colour:'#2BBBAD'}) create (f:Resources{resourceType:'overwatch',name_DE:'Überwachung',icon:'fa-eye',iconColour:'#fff',colour:'#aa66cc'}) create (g:Team{name:'red_giants'}) create (h:Team{name:'blue_dwarfs'}) create (i:Role{roleType:'ROLE_USER'}) create (j:User{uid:'pOtVsBtE', registrationDateTime:'2016-07-26 17:29:02',password:'$2y$13$L36wWIC3GdeugizC8PaAtejVRn/FJnJlHVwSCt8mPnh0ZUtSoyRRi',isAccountNonExpired:true,isCredentialsNonExpired:true,isEnabled:true,isAccountNonLocked:true,usernameCanonical:'test',isActive:true,emailCanonical:'mfbaer@gmail.com',email:'mfbaer@gmail.com',username:'test'}) create (j)-[k:HAS_RESOURCE{amount:5}]->(a) create (j)-[l:HAS_RESOURCE{amount:15}]->(b) create (j)-[m:HAS_RESOURCE{amount:10}]->(c) create (j)-[n:HAS_RESOURCE{amount:25}]->(d) create (j)-[o:HAS_RESOURCE{amount:20}]->(e) create (j)-[p:HAS_RESOURCE{amount:30}]->(f) create (j)-[q:HAS_ROLE]->(i) create (j)-[r:IN_TEAM]->(h)");
        
         die;
        return false;
    }
    
     public function test3Action()
    { 

        $em = $this->get('neo4j.graph_manager')->getClient();
        
        // DROP ALL DATA IN GRAPH !!!CAREFULL!!!:
        $em->getDatabaseDriver()->run("match (n)-[r]-() delete r,n");
        $em->getDatabaseDriver()->run("match (n) delete n");
        echo '<p style="color:green">DB CLEARED</p><br>';        
        
        // QUERY TO CREATE TEST DATA IN DB
        $em->getDatabaseDriver()->run("create (a:Resources{resourceType:'wood',name_DE:'Holz',icon:'fa-tree',iconColour:'#fff',colour:'#00C851'}) create (b:Resources{resourceType:'stone',name_DE:'Stein',icon:'fa-cubes',iconColour:'#fff',colour:'#a1887f'}) create (c:Resources{resourceType:'food',name_DE:'Nahrung',icon:'fa-cutlery',iconColour:'#fff',colour:'#fb8c00'}) create (d:Resources{resourceType:'water',name_DE:'Wasser',icon:'fa-tint',iconColour:'#fff',colour:'#33b5e5'}) create (e:Resources{resourceType:'work',name_DE:'Arbeitskraft',icon:'fa-industry',iconColour:'#fff',colour:'#2BBBAD'}) create (f:Resources{resourceType:'overwatch',name_DE:'Überwachung',icon:'fa-eye',iconColour:'#fff',colour:'#aa66cc'}) create (g:Team{name:'red_giants'}) create (h:Team{name:'blue_dwarfs'}) create (i:Role{roleType:'ROLE_USER'}) create (j:User{uid:'pOtVsBtE', registrationDateTime:'2016-07-26 17:29:02',password:'$2y$13\$L36wWIC3GdeugizC8PaAtejVRn/FJnJlHVwSCt8mPnh0ZUtSoyRRi',isAccountNonExpired:true,isCredentialsNonExpired:true,isEnabled:true,isAccountNonLocked:true,usernameCanonical:'test',isActive:true,emailCanonical:'mfbaer@gmail.com',email:'mfbaer@gmail.com',username:'test'}) create (j)-[k:HAS_RESOURCE{amount:5}]->(a) create (j)-[l:HAS_RESOURCE{amount:15}]->(b) create (j)-[m:HAS_RESOURCE{amount:10}]->(c) create (j)-[n:HAS_RESOURCE{amount:25}]->(d) create (j)-[o:HAS_RESOURCE{amount:20}]->(e) create (j)-[p:HAS_RESOURCE{amount:30}]->(f) create (j)-[q:HAS_ROLE]->(i) create (j)-[r:IN_TEAM]->(h) create (s:User{uid:'dppnECqN',registrationDateTime:'2016-07-27 09:13:49',password:'$2y$13\$I2zRT15T0BMRA.9TK3b0oevho646UZ/ZOw3Sg/eBVS7YritYPtKWC',isAccountNonExpired:true,isCredentialsNonExpired:true,isEnabled:true,isAccountNonLocked:true,usernameCanonical:'admin',isActive:true,emailCanonical:'manuel.baer@geonet.ch',email:'manuel.baer@geonet.ch',username:'admin'}) create (t:Role{roleType:'ROLE_ADMIN'}) create (s)-[u:HAS_ROLE]->(i) create (s)-[v:HAS_ROLE]->(t)");
        echo '<p style="color:green">TEST DB RECREATED</p><br>';  
       

        
        // echo 'Loading user with name: test <br>';        
        // $user=$em->getRepository(User::class)->findOneBy('username','test');
        // if($user){
        //     echo '<p style="color:green">OK</p><br>';
        // }else{
        //     echo '<p style="color:red">NOT OK</p><br>';
        //     echo '<p style="color:red">Properties were deleted!</p><br>';
        // }
        // 
        // echo 'Loading user with name: test <br>';        
        // $user=$em->getRepository(User::class)->findOneBy('username','admin');
        // if($user){
        //     echo '<p style="color:green">OK</p><br>';
        // }else{
        //     echo '<p style="color:red">NOT OK</p><br>';
        //     echo '<p style="color:red">Properties were deleted!</p><br>';
        // }

        
        $user = new User();
        echo '<p style="color:green">new user created</p><br>';  
        $user->setUsername('Foobar');
        echo '<p style="color:green">new user set username = Foobar</p><br>';       
        $role=$em->getRepository(Role::class)->findOneBy('roleType', 'ROLE_USER');  
        echo '<p style="color:green">Role node gotten</p><br>';  
        
        $user->addRole($role);
        echo '<p style="color:green"Added user to role</p><br>';  
        // 4) save the User!
        $em->persist($user);
        $em->flush();
        echo '<p style="color:green">persisted and flushed</p><br>';  
        
        echo 'Loading user with name: test <br>';        
        $user=$em->getRepository(User::class)->findOneBy('username','test');
        if($user){
            echo '<p style="color:green">OK</p><br>';
        }else{
            echo '<p style="color:red">NOT OK</p><br>';
            echo '<p style="color:red">Properties were deleted!</p><br>';
        }
        
        echo 'Clear EM<br>';        
        $em->clear();
        echo '<p style="color:green">OK</p><br>';        
        
        
       die;
       return false;
    }

}
