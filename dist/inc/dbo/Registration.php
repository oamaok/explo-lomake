<?php

/**
 * Class Registration
 * @property integer $id
 */
class Registration extends DbRecord {

    public function tableName()
    {
        return "registrations";
    }

    /**
     * @param string $className
     * @return Registration
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
} 