<?php

/**
 * Class Activity
 * @property integer $id
 */
class Activity extends DbRecord {

    public function tableName()
    {
        return "activities";
    }

    /**
     * @param string $className
     * @return Activity
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
} 