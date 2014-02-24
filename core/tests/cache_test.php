<?php
class cache_test extends Test {
    
    public $instance;//cache instance
    
    public function __construct()
    {
        //$this->instance=Cache::get_instance(CACHE_TYPE,CACHE_EXPIRE,CACHE_CONFIG);
    }
    
    public function start_cache_test()
    {
        self::add_note(''.CACHE_TYPE.' - '.CACHE_EXPIRE.' - ');//.CACHE_CONFIG
        $this->instance=Cache::get_instance(CACHE_TYPE,CACHE_EXPIRE,CACHE_CONFIG);
        return ($this->instance)? TRUE:FALSE;
    }
    
    public function cache_variable_test()
    {
        self::add_note('cache variable, with value');
        $this->instance->cache('variable','value stored');
        return ($this->instance->variable==='value stored')? TRUE:FALSE;
    }
    
    public function delete_variable_test()
    {
        $this->instance->variable='variable value to delete';
        $this->instance->delete('variable');//or unset($this->instance->delete);
        self::add_note('deleting variable : '.$this->instance->variable);
        return (empty($this->instance->variable))? TRUE:FALSE;
    }
    
    public function clear_test()
    {
        //first we are sure theres variables
        $this->instance->variable2='value2';
        $this->instance->variable3='asdasd';
        $this->instance->clear();
        self::add_note('clearing all variables : '. $this->instance->variable2);
        return (empty($this->instance->variable2) && empty($this->instance->variable3))? TRUE:FALSE;
    }
   
}
