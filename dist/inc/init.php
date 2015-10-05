<?php
setlocale(LC_ALL, "fi_FI");

spl_autoload_register(function($className) {
    $includePath = dirname(__FILE__);
    if(file_exists($includePath . DIRECTORY_SEPARATOR . $className . ".php"))
    {
        include_once $includePath . DIRECTORY_SEPARATOR . $className . ".php";
        return;
    }
    $includeDirectory = scandir($includePath);

    foreach($includeDirectory as $file)
    {
        if(!is_dir($includePath . DIRECTORY_SEPARATOR . $file))
            continue;
        if(file_exists($includePath . DIRECTORY_SEPARATOR . $file . DIRECTORY_SEPARATOR . $className . ".php"))
        {
            include_once $includePath . DIRECTORY_SEPARATOR . $file . DIRECTORY_SEPARATOR . $className . ".php";
            return;
        }
    }
    /*
     * TODO:
     *      Throw class not found exception or handle the error in another way
     */
});

set_include_path(dirname(__FILE__) . "/../");

?>