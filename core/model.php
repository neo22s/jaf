<?php
/**
 * Model base class. All models should extend this class.
 * 
 * You can create an instance of a model in this 3 ways:
 * $anymodel = new Model_anymodel(); //class
 * $anymodel = new Model('tablename','tableindex');//ORM style
 * $anymodel = Model::factory('anymodel');//factory
 * 
 * Later you can:
 * $anymodel->someindexfield=1;
 * $anymodel->select(1);
 * $anymodel->someDBfield='test';
 * $anymodel->save();
 *
 * @package     JAF
 * @subpackage  Core
 * @category    Model
 * @author      Chema Garrido <chema@garridodiaz.com>
 * @license     GPL v3
 */

class Model {

    
    public $data=array();//fields on the DB
    public $db; //db instance
    public $table_name; //table name on the DB
    public $table_index; // index of the table
    
    /**
     * Public construct, sets the DB connection
     */
    public function __construct($table_name=NULL,$table_index=NULL)
    {
        if($table_name!==NULL) $this->table_name=$table_name;
        if($table_index!==NULL) $this->table_index=$table_index;
        
        $this->db=DB::get_instance();
    }
    
    /**
     * Populates the data from the DB to the model
     * @param string $where overwrites the default select by the index
     * @param int $limit limit of results to return, if 1 populates more returns array
     * @return Model
     */
    public function select($where=NULL,$limit=1)
    {
        //if isnumeric we try to be friendly and use it as the index
        if (is_numeric($where))
        {
            $this->data[$this->table_index]=$where;
            $where=NULL;
        } 
        
        //query construction
        if ($where==NULL)
        {
           $where = $this->table_index.' = \''.$this->data[$this->table_index].'\''; 
        }
        
        $query = 'SELECT * FROM `'.$this->table_name.'`
        		  WHERE '.$where.' LIMIT '.$limit;

        //populating the data from the DB
        $data=$this->db->get_array($query);

        if ($data==NULL)//nothing found
        {
            return NULL;
        }
        elseif(count($data)==1)//case we populate the object
        {
            $this->data=$data[0];
        }
        elseif(count($data)>1)
        {// return array of Models
            $class = get_class($this);//to be same as model called
            $ret = array();
            foreach ($data as $d)
            {
                $model = new $class;
                $model->data=$d;
                $ret[]= $model;
                unset($model);
            }
            return $ret;
        }
        
    }
    
    /**
     * Inserts the model values in the DB
     */
    public function create()
    {
        //just in case you call create() with an index defined
        $data=$this->data;
        if (array_key_exists($this->table_index, $data))
        {
            unset($data[$this->table_index]);
        }
        
        $this->data[$this->table_index] = $this->db->insert($this->table_name,$data);
    }
    
    /**
     * update the current model from the DB
     */
    public function update()
    {
        $data=$this->data;
        if (array_key_exists($this->table_index, $data))
        {
            unset($data[$this->table_index]);
        }
        
        $where = $this->table_index.' = \''.$this->data[$this->table_index].'\' LIMIT 1';        
        $this->db->update($this->table_name, $data,$where);        
    }
    
    /**
     * deletes the current model from the DB
     * @param string $where overwrites the default select by the index
     * @param int $limit limit of results to return, if 1 populates more returns array
     */
    public function delete($where=NULL,$limit=1)
    {
        //if isnumeric we try to be friendly and use it as the index
        if (is_numeric($where))
        {
            $this->data[$this->table_index]=$where;
            $where=NULL;
        } 
        
        if ($where==NULL)//delete
        {
           $where = $this->table_index.' = \''.$this->data[$this->table_index].'\' LIMIT 1'; 
           $ret = $this->db->delete($this->table_name,$where);
        }
        else//custom delete
        {
            $query = 'DELETE FROM `'.$this->table_name.'`
        		  WHERE '.$where.' LIMIT '.$limit;
            $ret = $this->db->query($query);
        }
        //if succeeded we unload the data
        if ($ret) $this->unload();
    }
    
    /**
     * saves the model into the DB
     */
    public function save()
    {
        //if index exists calls update if doesn't exists create
        ($this->loaded())? $this->update():$this->create();
    }
    
    /**
     * Just tells you if there's some data populated in the model
     * @return boolean
     */
    public function loaded()
    {
        return (array_key_exists($this->table_index, $this->data))?TRUE:FALSE;
    }
    
    /**
     * Unloads any data
     */
    public function unload()
    {
        unset($this->data);
    }
    
	/**
	 * Create a new model instance.
	 *
	 *     $model = Model::factory($name);
	 *
	 * @param   string   model name
	 * @return  Model
	 */
	public static function factory($name)
	{
	    $class = 'Model_'.$name;
	    if(class_exists($class))
        {
    		return new $class;
        }
		else
		{
		    log::add('Model::Factory model not found: '.$name);
		}
	}
	
    
	/**
	 * Automatically executed before the model action.
	 */
	public static function before()
	{
		// hook by default
		do_action(get_called_class().'_before');
	}

	/**
	 * Automatically executed after the model action. 
	 */
	public static function after()
	{
		// hook by default
		do_action(get_called_class().'_after');
	}
		
	/**
	 * Magic methods to set get
	 */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        return (array_key_exists($name, $this->data)) ? $this->data[$name] : NULL;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }
    
}