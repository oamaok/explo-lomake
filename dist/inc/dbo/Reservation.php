<?php

/**
 * Class Reservation
 * @property integer $id
 */
class Reservation extends DbRecord {

    public function tableName()
    {
        return "reservations";
    }

    /**
     * @param string $className
     * @return Reservation
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
} 