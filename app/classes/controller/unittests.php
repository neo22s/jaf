<?php
class Controller_unittests extends Controller{
    
    public function index()
    {
        if (DEBUG==TRUE)
        {
            /*
            H::load_file(CORE_PATH.'tests/cache_test.php');
            $test = new cache_test;
            $test->set_path(CORE_PATH.'tests/');
            $test->run();*/

            $test = new Test();
            $test->run_all(CORE_PATH.'tests/');
            $test->get_results();
        }
        else
        {
            H::redirect(H::get_domain());
        }
        
    }
}