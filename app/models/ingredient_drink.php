<?php

class Ingredient_Drink extends BaseModel{
    
    public $ingredient_id, $drink_id;

    public function __construct($attributes){
        parent::__construct($attributes);
    }
    
}