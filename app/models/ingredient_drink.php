<?php

class Ingredient_Drink extends BaseModel{
    
    public $ingredient_id, $drink_id;

    public function __construct($attributes){
        parent::__construct($attributes);
    }
    
    public function save(){
        $query = DB::connection()->prepare('INSERT INTO Ingredient_Drink (ingredient_id, drink_id) VALUES (:ingredient_id, :drink_id)');

        $query->execute(array('ingredient_id' => $this->ingredient_id, 'drink_id' => $this->drink_id));
    }
}