<?php
namespace JoranBeaufort\Neo4jUserBundle\Controller;

use GraphAware\Neo4j\OGM\EntityManager;
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
use AppBundle\Entity\Tile;
use AppBundle\Entity\Team;
use AppBundle\Entity\UserTeam;
use AppBundle\Entity\UserTile;
use AppBundle\Entity\TileDrone;
use AppBundle\Entity\Drone;

class TestController extends Controller
{
    
     public function testAction($token)
    {

        /** @var EntityManager $em */
        $em = $this->get('neo4j.graph_manager')->getClient();

        $usernameInput = 'test';
        $resourceInput1 = 'stone';
        $resourceInput2 = 'water';
        $resourceInput3 = 'food';
        

        
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
        echo '<pre>';
        $em = $this->get('neo4j.graph_manager')->getClient();
        $tile = $em->getRepository(Tile::class)->findOneById(intval(1823));

        // $user = $em->getRepository(User::class)->findOneById(1784);
        // foreach($user->getUserTiles() as $t){
        //     var_dump($t->getTile()->getResources());
        // }die;
        echo sys_get_temp_dir();
        var_dump(is_writable(sys_get_temp_dir()));
        var_dump($tile->getUserTile()->getCollected());
        var_dump($tile->getUserTile());
        exit();
        $userTile = $em->getRepository(User::class)->findOneBy('uid',$tile->getUid());
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

        echo '<pre>';
        $em = $this->get('neo4j.graph_manager')->getClient();

        // // DROP ALL DATA IN GRAPH !!!CAREFULL!!!:
        // $em->getDatabaseDriver()->run("match (n)-[r]-() delete r,n");
        // $em->getDatabaseDriver()->run("match (n) delete n");
        // echo '<p style="color:green">DB CLEARED</p><br>';
        //
        // // QUERY TO CREATE TEST DATA IN DB
        // $em->getDatabaseDriver()->run("create (a:Resources{resourceType:'wood',name_DE:'Holz',icon:'fa-tree',iconColour:'#fff',colour:'#00C851'}) create (b:Resources{resourceType:'stone',name_DE:'Stein',icon:'fa-cubes',iconColour:'#fff',colour:'#a1887f'}) create (c:Resources{resourceType:'food',name_DE:'Nahrung',icon:'fa-cutlery',iconColour:'#fff',colour:'#fb8c00'}) create (d:Resources{resourceType:'water',name_DE:'Wasser',icon:'fa-tint',iconColour:'#fff',colour:'#33b5e5'}) create (e:Resources{resourceType:'work',name_DE:'Arbeitskraft',icon:'fa-industry',iconColour:'#fff',colour:'#2BBBAD'}) create (f:Resources{resourceType:'overwatch',name_DE:'Überwachung',icon:'fa-eye',iconColour:'#fff',colour:'#aa66cc'}) create (g:Team{name:'red_giants'}) create (h:Team{name:'blue_dwarfs'}) create (i:Role{roleType:'ROLE_USER'}) create (j:User{uid:'pOtVsBtE', registrationDateTime:'2016-07-26 17:29:02',password:'$2y$13\$L36wWIC3GdeugizC8PaAtejVRn/FJnJlHVwSCt8mPnh0ZUtSoyRRi',isAccountNonExpired:true,isCredentialsNonExpired:true,isEnabled:true,isAccountNonLocked:true,usernameCanonical:'test',isActive:true,emailCanonical:'mfbaer@gmail.com',email:'mfbaer@gmail.com',username:'test'}) create (j)-[k:HAS_RESOURCE{amount:5}]->(a) create (j)-[l:HAS_RESOURCE{amount:15}]->(b) create (j)-[m:HAS_RESOURCE{amount:10}]->(c) create (j)-[n:HAS_RESOURCE{amount:25}]->(d) create (j)-[o:HAS_RESOURCE{amount:20}]->(e) create (j)-[p:HAS_RESOURCE{amount:30}]->(f) create (j)-[q:HAS_ROLE]->(i) create (j)-[r:IN_TEAM]->(h) create (s:User{uid:'dppnECqN',registrationDateTime:'2016-07-27 09:13:49',password:'$2y$13\$I2zRT15T0BMRA.9TK3b0oevho646UZ/ZOw3Sg/eBVS7YritYPtKWC',isAccountNonExpired:true,isCredentialsNonExpired:true,isEnabled:true,isAccountNonLocked:true,usernameCanonical:'admin',isActive:true,emailCanonical:'manuel.baer@geonet.ch',email:'manuel.baer@geonet.ch',username:'admin'}) create (t:Role{roleType:'ROLE_ADMIN'}) create (s)-[u:HAS_ROLE]->(i) create (s)-[v:HAS_ROLE]->(t)");
        // echo '<p style="color:green">TEST DB RECREATED</p><br>';
        // 
        // // DROP ALL DATA IN GRAPH !!!CAREFULL!!!:
        // $em->getDatabaseDriver()->run("match (n)-[r]-() delete r,n");
        // $em->getDatabaseDriver()->run("match (n) delete n");
        // echo '<p style="color:green">DB CLEARED</p><br>';        
        // 
        // // QUERY TO CREATE TEST DATA IN DB
        // $em->getDatabaseDriver()->run("create (a:Resources{resourceType:'wood',name_DE:'Holz',icon:'fa-tree',iconColour:'#fff',colour:'#00C851'}) create (b:Resources{resourceType:'stone',name_DE:'Stein',icon:'fa-cubes',iconColour:'#fff',colour:'#a1887f'}) create (c:Resources{resourceType:'food',name_DE:'Nahrung',icon:'fa-cutlery',iconColour:'#fff',colour:'#fb8c00'}) create (d:Resources{resourceType:'water',name_DE:'Wasser',icon:'fa-tint',iconColour:'#fff',colour:'#33b5e5'}) create (e:Resources{resourceType:'work',name_DE:'Arbeitskraft',icon:'fa-industry',iconColour:'#fff',colour:'#2BBBAD'}) create (f:Resources{resourceType:'overwatch',name_DE:'Überwachung',icon:'fa-eye',iconColour:'#fff',colour:'#aa66cc'}) create (g:Team{name:'red_giants'}) create (h:Team{name:'blue_dwarfs'}) create (i:Role{roleType:'ROLE_USER'}) create (j:User{uid:'pOtVsBtE', registrationDateTime:'2016-07-26 17:29:02',password:'$2y$13\$L36wWIC3GdeugizC8PaAtejVRn/FJnJlHVwSCt8mPnh0ZUtSoyRRi',isAccountNonExpired:true,isCredentialsNonExpired:true,isEnabled:true,isAccountNonLocked:true,usernameCanonical:'test',isActive:true,emailCanonical:'mfbaer@gmail.com',email:'mfbaer@gmail.com',username:'test'}) create (j)-[k:HAS_RESOURCE{amount:5}]->(a) create (j)-[l:HAS_RESOURCE{amount:15}]->(b) create (j)-[m:HAS_RESOURCE{amount:10}]->(c) create (j)-[n:HAS_RESOURCE{amount:25}]->(d) create (j)-[o:HAS_RESOURCE{amount:20}]->(e) create (j)-[p:HAS_RESOURCE{amount:30}]->(f) create (j)-[q:HAS_ROLE]->(i) create (j)-[r:IN_TEAM]->(h) create (s:User{uid:'dppnECqN',registrationDateTime:'2016-07-27 09:13:49',password:'$2y$13\$I2zRT15T0BMRA.9TK3b0oevho646UZ/ZOw3Sg/eBVS7YritYPtKWC',isAccountNonExpired:true,isCredentialsNonExpired:true,isEnabled:true,isAccountNonLocked:true,usernameCanonical:'admin',isActive:true,emailCanonical:'manuel.baer@geonet.ch',email:'manuel.baer@geonet.ch',username:'admin'}) create (t:Role{roleType:'ROLE_ADMIN'}) create (s)-[u:HAS_ROLE]->(i) create (s)-[v:HAS_ROLE]->(t)");
        // echo '<p style="color:green">TEST DB RECREATED</p><br>';  
       

        
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
    
     public function test4Action()
    {

        $em = $this->get('neo4j.graph_manager')->getClient();
        
        
        
        echo 'Loading tile with id: 1842 <br>';        
        $tile = $em->getRepository(Tile::class)->findOneById(1842);
        if($tile->getRid() == 4726){
            echo '<p style="color:green">OK</p><br>';
        }else{
            echo '<p style="color:red">NOT OK</p><br>';
        }
        
        echo 'Loading user connected to tile <br>';        
        $user = $tile->getUserTile()->getUser();
        if($user->getUsername() == 'mfbaer'){
            echo '<p style="color:green">OK</p><br>';
        }else{
            echo '<p style="color:red">NOT OK</p><br>';
        }
        
        echo 'Loading team of user connected to the tile. Starting at (user) node';        
        if($user->getUserTeam()->getTeam()->getName() == 'red_giants'){
            echo '<p style="color:green">OK</p><br>';
        }else{
            echo '<p style="color:red">NOT OK</p><br>';
        }
        
        echo 'Loading team of user connected to the tile. Starting at (tile) node';        
        if($tile->getUserTile()->getUser()->getUserTeam()->getTeam()->getName() == 'red_giants'){
            echo '<p style="color:green">OK</p><br>';
        }else{
            echo '<p style="color:red">NOT OK</p><br>';
        }
        
        echo 'Clear EM<br>';        
        $em->clear();
        echo '<p style="color:green">OK</p><br>';        

       die;
       return false;
    }
    
    
     public function test5Action()
    {

    
        $em = $this->get('neo4j.graph_manager')->getClient();
     
        $em->getDatabaseDriver()->run("match (n:Tile{uid:'test'})<-[c:CAPTURED]-(u:User) delete c,n");     
        
        $user=$em->getRepository(User::class)->findOneBy('username', 'test');  

                    
        echo 'Create new tile<br>';        
        $tile = new Tile('test',1234, 4.66, 8.77, '[1,2],[2,3],[3,4],[4,5]'); 
        echo '<p style="color:green">OK</p><br>';

        echo 'persist tile <br>';        
        $em->persist($tile);
        echo '<p style="color:green">OK</p><br>';

        echo 'add tile to user<br>';
        $user->addTile($tile, time(),time());
        echo '<p style="color:green">OK</p><br>';
        
        echo 'persist tile <br>'; 
        $em->persist($user);
        
        echo 'flush<br>'; 
        $em->flush();
                
        echo 'Clear EM<br>';        
        $em->clear();
        echo '<p style="color:green">OK</p><br>';        

       die;
       return false;
    }
    
     public function test6Action()
    {

        $em = $this->get('neo4j.graph_manager')->getClient();
        $em->getDatabaseDriver()->run("match (n:User{username:'foo'})-[r:HAS_ROLE]->(s:Role) delete r,n");     
        $em->getDatabaseDriver()->run("match (n:User{username:'foo'}) delete n");     
        
        echo 'Create new user';
        $user = new User();
        echo '... <p style="color:green">[OK]</p><br>';
        echo 'Add a few attr';
        $user->setUsername('foo');
        $user->setPassword('bar');
        $user->setIsEnabled(false);
        $user->setIsAccountNonExpired(true);
        $user->setIsAccountNonLocked(true);
        $user->setIsCredentialsNonExpired(true);
        echo '... <p style="color:green">[OK]</p><br>';
        
        /*
        echo 'Persist';
        $em->persist($user);
        echo '... <p style="color:green">[OK]</p><br>';
        echo 'Flush';
        $em->flush();
        echo '... <p style="color:green">[OK]</p><br>';
        
        $em->clear();
        */
        
        echo 'Get Role';
        $role=$em->getRepository(Role::class)->findOneBy('roleType', 'ROLE_USER');   
        echo '... <p style="color:green">[OK]</p><br>';
        echo 'Add role to user';
        $user->addRole($role);
        echo '... <p style="color:green">[OK]</p><br>';
       
        echo 'Persist';
        $em->persist($user);
        echo '... <p style="color:green">[OK]</p><br>';
        echo 'Flush';
        $em->flush();
        echo '... <p style="color:green">[OK]</p><br>';
        
        $em->clear();
        
        echo 'Get User out of DB';
        $user=$em->getRepository(User::class)->findOneBy('username', 'foo');
        if($user){
            echo '... <p style="color:green">[OK]</p><br>';
        }else{
            echo '... <p style="color:red">[error] - User is null</p><br>';
        }
        echo 'Get role of user';
        $r = $user->getRoles();
        echo '... <p style="color:green">[OK]</p><br>';
        
        echo 'Count resource nodes';
        $countResources = $em->getDatabaseDriver()->run("match (n:Resources) return count(*)");  
        print_r($countResources);
       die;
       return false;
    }
    
     public function test7Action()
    {
        

        $em = $this->get('neo4j.graph_manager')->getClient();
        
     //   $em->getDatabaseDriver()->run("match (n:User{username:'test'}), (t:Team{name:'red_giants'}) create (n)-[it:IN_TEAM]->(t)");     

        $user = $em->getRepository(User::class)->findOneBy('username','test');
        
        $tile = new Tile($user->getUid(),'123', 456, 789, 'bbox'); 
        $tile->setResources('1,2,3'); 
        $em->persist($tile);
        
        
        $drone = $em->getRepository(Drone::class)->findOneBy('name','nova_xs');
        $tile->setTileDrone($drone,$drone->getHp());

        $user->addTile($tile, time(),time());
        
        $em->persist($user);
        $em->flush();
        
        $em->clear();
        $user = $em->getRepository(User::class)->findOneBy('username','test');
        var_dump($user->getUserTeam()->getTeam());

       die;
       return false;
    }

}
