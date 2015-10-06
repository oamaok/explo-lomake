<?php

/**
 * Class Session
 * @property integer $id
 */
class Session extends DbRecord {

    public function tableName()
    {
        return "sessions";
    }

    /**
     * @param string $className
     * @return Session
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
} 