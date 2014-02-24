<?php
class Controller_example extends Controller{
    
    public function index()
    {
        $V=new View('home');
        $V->meta_title='examples';
        $V->content='Usage example';
        $V->render();
    }
    
    public function page($t)
    {
        $V=new View('home');
        $V->meta_title=$t;
        $V->meta_description=$t;
        $V->meta_keywords=$t;
        $V->content=$t;
        $V->render();
    }
    
    public function params($p1,$p2,$p3)
    {           
        $V=new View('home');
        $V->meta_title="$p1,$p2,$p3 wow title";
        $V->meta_description='wow desc';
        $V->meta_keywords="$p1,$p2,$p3";
        $V->content="$p1,$p2,$p3";
        
        $V->render();
    }
    
    public function db()
    {
        $V=new View('home');
        $V->meta_title='db test';
       
        $db=DB::get_instance();
         //$query = 'select * from oc_posts';
        //$res = $db->get_array($query);
        
        //echo $db->insert('oc_categories',array('name'=>'Inserted value 2','friendlyName'=>'inserted-value2','order'=>0));
        //$db->update('oc_categories',array('name'=>'Español','order'=>21),'idCategory=16');
        //$db->delete('oc_categories','idCategory=10');
                
        $V->content=' categories count: '.$db->get_value('select count(idCategory) from oc_categories');
        
        $post=new Model_post();
        //$post = new Model('oc_posts','idPost');
        //$post=Model::factory('post');
        //$post->idPost=1;
        //$post->select();
        $post->select(3);
        $V->content.=$post->description;
        
        //$ret=$post->select('isConfirmed=1',5);
        //die(print_r($post));
        
        //$post->create();
        
        //die(print_r($post->idPost));
        
       // $post->delete(25);
        //$post->delete('idPost>18',3);
        $post->title='updatedddd';
        $post->save();
        
        
        $V->render();          
    }
    
    public function date()
    {
        $V=new View('home');
        $V->meta_title='db date/php test';
        $db=DB::get_instance();
        
        $V->content .='mysql time:'.$db->now();
        $V->content .='<br />php time:'.$db->now('php');
        
        $V->render();    
    }
    
    
}