<?php

class Database {

    /**
     * @var mysqli
     */
    private static $mysqli;

    /**
     * @var array
     */
    private static $tables;

    /**
     * @return mixed
     *
     */
    public static function query()
    {
        $numArgs = func_num_args();

        if(!$numArgs)
        {
            return false;
        }
        $mysqli = Database::getMysqli();

        if($numArgs == 1)
        {
            $stmt = $mysqli->prepare(func_get_arg(0));
            if(!$stmt)
            {
                Logger::log('$mysqli->prepare failed (%s) (query="%s").', $mysqli->error, func_get_arg(0));
                return false;
            }
            $stmt->execute();
            if($stmt->errno)
            {
                Logger::log('$mysqli->execute failed (%s).', $stmt->error);
                return false;
            }
            $result = Database::fetch($stmt);
            $stmt->close();

            return $result;
        }

        // if the number of arguments is more than two

        $type = "";
        $arguments = array();

        for($i = 1; $i < $numArgs; $i++)
        {
            /*
             *
             * TODO:
             *      better error handling for invalid types
             *
             */
            $argument = func_get_arg($i);
            array_push($arguments, $argument);

            switch(gettype($argument))
            {
                case "string":
                    $type .= 's';
                    break;
                case "NULL":
                    $type .= 's';
                    break;
                case "double":
                    $type .= 'd';
                    break;
                case "integer":
                    $type .= 'i';
                    break;
                case "boolean":
                    $type .= 'i';
                    break;
                default:
                    return false;
                    break;
            }
        }

        array_unshift($arguments, $type);
        $stmt = $mysqli->prepare(func_get_arg(0));
        if(!$stmt)
        {
            Logger::log('$mysqli->prepare failed (%s) (query="%s").', $mysqli->error, func_get_arg(0));
            return false;
        }

        if(!call_user_func_array(array($stmt, "bind_param"), CommonUtil::arrayToReferences($arguments)))
        {
            Logger::log('$mysqli->bind_param failed.');
            return false;
        }
        $stmt->execute();
        if($stmt->errno)
        {
            Logger::log('$mysqli->execute failed (%s).', $stmt->error);
            return false;
        }
        if($stmt->affected_rows == -1)
            $result = Database::fetch($stmt);
        else
            $result = $stmt->insert_id;

        $stmt->close();

        return $result;
    }

    /**
     * @return array
     */
    public static function getTables()
    {
        if(!isset(Database::$tables))
        {
            $tables = Database::query("SHOW TABLES");
            Database::$tables = array();
            foreach($tables as $table)
            {
                array_push(Database::$tables, array_pop($table));
            }
        }

        return Database::$tables;
    }

    /**
     * @param $stmt mysqli_stmt
     * @return mixed
     */
    private static function fetch($stmt)
    {
        $stmt->store_result();

        $variables = array();
        $data = array();
        $meta = $stmt->result_metadata();

        if(!$meta)
            return true;

        while($field = $meta->fetch_field())
            $variables[] = &$data[$field->name];

        call_user_func_array(array($stmt, 'bind_result'), $variables);

        $i = 0;
        $result = array();
        while($stmt->fetch())
        {
            $result[$i] = array();
            foreach($data as $k=>$v)
                $result[$i][$k] = $v;
            $i++;
        }

        return $result;
    }

    /**
     * @return mysqli
     */
    private static function getMysqli()
    {
        if(Database::$mysqli)
            return Database::$mysqli;

        Database::$mysqli = new mysqli(
            Config::DB_HOSTNAME,
            Config::DB_USERNAME,
            Config::DB_PASSWORD,
            Config::DB_DATABASE
        );
        if(!Database::$mysqli)
        {
            exit(0);
        }
        Database::$mysqli->set_charset("utf8");

        register_shutdown_function("Database::close");

        return Database::$mysqli;
    }

    /**
     *
     */
    public static function close()
    {
        if(Database::$mysqli)
            Database::$mysqli->close();
    }

    /**
     * @return string
     */
    public static function lastError()
    {
        return Database::$mysqli->error;
    }

    public static function convertCase($columnName)
    {
        $columnName = str_replace("_", " ", $columnName);
        $columnName = ucwords($columnName);
        $columnName = str_replace(" ", "", $columnName);
        $columnName = lcfirst($columnName);

        return $columnName;
    }

    public static function now()
    {
        return date("Y-m-d H:i:s");
    }
} 