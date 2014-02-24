<?php
class Controller_home extends Controller{
    
    public function index()
    {
        $V=new View('home');
        $V->meta_title='JAF-PHP';
        //$V->meta_description='Just Another Framework Home Just Another Framework Home Just Another Framework Home Just Another Framework Home Just Another Framework Home Just Another Framework Home Just Another Framework Home Just Another Framework HomeJust Another Framework HomeJust Another Framework HomeJust Another Framework HomeJust Another Framework HomeJust Another Framework HomeJust Another Framework HomeJust Another Framework HomeJust Another Framework HomeJust Another Framework HomeJust Another Framework HomeJust Another Framework HomeJust Another Framework HomeJust Another Framework HomeJust Another Framework HomeJust Another Framework HomeJust Another Framework HomeJust Another Framework HomeJust Another Framework HomeJust Another Framework HomeJust Another Framework Home';
        //$V->meta_keywords='Just Another Framework Home';
        //$V->content='Hello World!';
        $V->content="It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).";
        
        $V->render();
    }
    
    public function readme()
    {  
        $V=new View('home');
        $V->content=H::nl2br(file_get_contents(BASE_PATH.'README.TXT'));
        //H::email('neo22s@gmail.com','chema@garridodiaz.com','test email jaf', $V->content);
        $V->meta_title='Readme';  
        $V->meta_decription='Readme description';    
        $V->render();
    } 
    
    public function not_found($t)
    {
        $V=new View('home');
        $V->content='The url: '.SITE_URL.'/'.$t.' - Was not found in the system.';
        $V->meta_title='404 - Not Found';     
        $V->render();
    }

}