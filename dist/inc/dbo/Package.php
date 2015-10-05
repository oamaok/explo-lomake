<?php

/**
 * Class Package
 * @property integer $id
 */
class Package extends DbRecord {

    public function tableName()
    {
        return "packages";
    }

    /**
     * @param string $className
     * @return Package
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
} 