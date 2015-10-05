<?php

abstract class DbRecord {

    /**
     * 
     * Constants used by orderBy function.
     *
     */
    const ORDER_ASC = 0;
    const ORDER_DESC = 1;

    /**
     * @return string
     *
     */
    abstract public function tableName();

    /**
     * @var array
     *
     * Stores the table structure.
     */
    private $tableStructure;

    /**
     * @var int
     *
     * Stores the primary key of the record if the record
     * is either fetched from or pushed to the database.
     */
    private $primaryKey = null;

    /**
     * @var string
     *
     * Stores the name of the primary key column.
     */
    private $primaryKeyColumn;

    /**
     * @var array
     */
    private $fieldValues = array();

    /**
     *	@var array
     *  
     *  
     */
    private $dirtyFields = array();


    /**
     *  @var string
     *
     *
     */
    private $orderByColumn = null;

    /**
     *  @var int
     *
     *
     */
    private $orderDirection = 0;
    
    /**
     *  @var int
     *
     *
     */
    private $limit = null;
    
    /**
     *  @var int
     *
     *
     */
    private $limitOffset = null;


    /**
     * @var array
     *
     * Stores models of derived classes. The models store
     * the information about table structure, etc.
     */
    private static $models = array();

    public function __construct()
    {
        $className = get_class($this);
        if(isset(self::$models[$className]))
        {
            $model = self::$models[$className];
            $this->fieldValues = $model->fieldValues;
            return;
        }

        $columns = $this->getTableStructure();
        foreach($columns as $column)
        {
            $field = $column["Field"];
            $default = $column["Default"];
            $this->fieldValues[$field] = $default;
        }
        $this->getPrimaryKeyColumn();
        self::$models[$className] = $this;
    }

    public function __get($var)
    {
        if(array_key_exists($var, $this->fieldValues))
            return $this->fieldValues[$var];

        return $this->$var;
    }

    public function __set($var, $val)
    {
        if(array_key_exists($var, $this->fieldValues))
        {
            if($this->getPrimaryKeyColumn() == $var)
                $this->primaryKey = $val;
            $this->fieldValues[$var] = $val;
            $this->dirtyFields[$var] = true;
            return;
        }

        $this->$var = $val;
    }

    /**
     *
     *
     */
    public function save()
    {
        if($this->primaryKey)
        {
            $values = "";
            $arguments = array();
            foreach($this->fieldValues as $field => $value)
            {
                if($field == $this->getPrimaryKeyColumn())
                    continue;

                // check if the field is marked as dirty
                if(!(isset($this->dirtyFields[$field]) && $this->dirtyFields[$field] === true))
                	continue;

                $values .= "," . $field . "=?";
                array_push($arguments, $value);
            }
            // trim out the preceding comma
            $values = substr($values, 1);
            $table = $this->tableName();
            
            $condition = $this->getPrimaryKeyColumn() . "=?";
            array_push($arguments, $this->primaryKey);
            $query = "UPDATE $table SET $values WHERE $condition";
            array_unshift($arguments, $query);

            call_user_func_array("Database::query", $arguments);

            // fetch the updated value and replace $this with it
            $updated = $this->findByPk($this->primaryKey);
            $this->copy($updated);

            // reset dirtyFields as we just saved them into the database
            $this->dirtyFields = array();

            return $this->primaryKey;
        }
        else
        {
            $columns = "";
            $values = "";
            $arguments = array();
            foreach($this->fieldValues as $field => $value)
            {
                if($field == $this->getPrimaryKeyColumn())
                    continue;
                $columns .= "," . $field;
                $values .= ",?";
                array_push($arguments, $value);
            }

            // trim out the preceding commas
            $columns = substr($columns, 1);
            $values = substr($values, 1);

            $table = $this->tableName();
            $query = "INSERT INTO $table ($columns) VALUES ($values)";
            array_unshift($arguments, $query);

            $primaryKey = call_user_func_array("Database::query", $arguments);
            $updated = $this->findByPk($primaryKey);
            $this->copy($updated);
            return $primaryKey;
        }
    }

    /**
     * @param $object
     *
     * Copies an object to this.
     */
    public function copy($object)
    {
        foreach($object->fieldValues as $field => $value)
        {
            if($field == $this->getPrimaryKeyColumn())
                $this->primaryKey = $value;
            $this->fieldValues[$field] = $value;
        }
    }

    public function delete()
    {
    	if(!$this->primaryKey)
    		return;
    	
    	$query = sprintf("DELETE FROM %s WHERE %s = ?", $this->tableName(), $this->getPrimaryKeyColumn());
    	Database::query($query, $this->primaryKey);
    }

    /**
     * @param $value int
     * @return object
     *
     * Finds an object by primary key.
     */
    public function findByPk($value)
    {
        $query = sprintf("SELECT * FROM %s WHERE %s = ?", $this->tableName(), $this->getPrimaryKeyColumn());

        $record = Database::query($query, $value);
        if(!$record)
            return null;
        return $this->fromRecord($record[0]);
    }

    public function orderBy($column, $order = self::ORDER_ASC)
    {
        $this->orderDirection = $order;
        $this->orderByColumn = $column;
        return $this;
    }

    public function limit($limit, $offset = null)
    {
        $this->limit = $limit;
        $this->limitOffset = $offset;
        return $this;
    }

    /**
     * @return object
     *
     * Finds an object by given criteria.
     */
    public function find()
    {
        $query = "SELECT * FROM " . $this->tableName();
        $arguments = array();

        if(func_num_args())
        {
            $query .= " WHERE " . func_get_arg(0);

            for($i = 1; $i < func_num_args(); $i++)
            {
                array_push($arguments, func_get_arg($i));
            }
        }

        if($this->orderByColumn !== null)
        {
            $query .= " ORDER BY " . $this->orderByColumn . ($this->orderDirection ? " DESC" : " ASC");
            $this->orderByColumn = null;
        }

        // only return the first matching object
        $query .= " LIMIT 1";
        array_unshift($arguments, $query);

        $records = call_user_func_array("Database::query", $arguments);

        if(!$records)
            return null;

        return $this->fromRecord($records[0]);
    }

    /**
     * @return array
     *
     * Finds all objects by given criteria.
     */
    public function findAll()
    {
        $query = "SELECT * FROM " . $this->tableName();
        $arguments = array();

        if(func_num_args())
        {
            $query .= " WHERE " . func_get_arg(0);

            for($i = 1; $i < func_num_args(); $i++)
            {
                array_push($arguments, func_get_arg($i));
            }
        }

        if($this->orderByColumn !== null)
        {
            $query .= " ORDER BY " . $this->orderByColumn . ($this->orderDirection ? " DESC" : " ASC");
            $this->orderByColumn = null;
        }

        if($this->limit !== null)
        {
            $query .= " LIMIT ?";
            array_push($arguments, $this->limit);
            $this->limit = null;
            if($this->limitOffset !== null)
            {
                $query .= " OFFSET ?";
                array_push($arguments, $this->limitOffset);
                $this->limitOffset = null;
            }
        }

        array_unshift($arguments, $query);

        $records = call_user_func_array("Database::query", $arguments);

        if(!$records)
            return array();

        $objects = array();
        foreach($records as $record)
        {
            array_push($objects, $this->fromRecord($record));
        }
        return $objects;
    }

    public function fromRecord($record)
    {
        $className = get_called_class();
        $object = new $className;

        foreach($record as $field => $value)
        {
            if(array_key_exists($field, $object->fieldValues))
            {
                $object->$field = $record[$field];
            }
        }

        return $object;
    }

    /**
     * @return array
     *
     * Returns table structure for table returned by tableName()
     */
    private function getTableStructure()
    {
        // if the model is already stored, fetch the value from it.
        if(isset(self::$models[get_class($this)]))
            return self::$models[get_class($this)]->tableStructure;

        if(!isset($this->tableStructure))
        {
            // get table names in the database
            $tables = Database::getTables();

            // check if the table is found in the database
            if(!in_array($this->tableName(), $tables))
            {
                Logger::log("Table '%s' doesn't exist in database %s!", $this->tableName(), Config::DB_DATABASE);
                return array();
            }
            // get table structure from the database
            $this->tableStructure = Database::query("SHOW FULL COLUMNS FROM " . $this->tableName());
            if(!$this->tableStructure)
                return array();
        }
        return $this->tableStructure;
    }

    /**
     * @return string
     *
     * Returns the name of the primary key column from the
     * table returned by tableName().
     *
     */
    public function getPrimaryKeyColumn()
    {
        // if the model is already stored, fetch the value from it.
        if(isset(self::$models[get_class($this)]))
            return self::$models[get_class($this)]->primaryKeyColumn;

        if(!isset($this->primaryKeyColumn))
        {
            $columns = $this->getTableStructure();
            foreach($columns as $column)
            {
                if($column["Key"] == "PRI")
                {
                    $this->primaryKeyColumn = $column["Field"];
                    break;
                }
            }
        }

        return $this->primaryKeyColumn;
    }
    /**
     * @param string $className
     * @return object
     *
     * Returns the model object, or if not set, creates a
     * new one and returns it.
     */

    public static function model($className = __CLASS__)
    {
        if(isset(self::$models[$className]))
            return self::$models[$className];
        else
        {
            $class = self::$models[$className] = new $className;
            return $class;
        }
    }
} 
