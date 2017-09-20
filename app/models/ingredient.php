<?php

class Ingredient extends BaseModel{
    
    public $id, $name, $alcohol_percentage, $description;

    public function __construct($attributes){
        parent::__construct($attributes);
    }
    
    public static function all() {
        $query = DB::connection()->prepare('SELECT * FROM Ingredient');
        $query->execute();
        
        $rows = $query->fetchAll();
        $ingredients = array();
        
        foreach($rows as $row) {
            
            $ingredients[] = new Ingredient(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'alcohol_percentage' => $row['alcohol_percentage'],
                'description' => $row['description']
            ));
        }
        return $ingredients;
    }
    
     public static function single($id) {
        $query = DB::connection()->prepare('SELECT * FROM Ingredient WHERE id = :id');
        $query->execute(array('id' => $id));

        $row = $query->fetch();
        
        if($row) {
            $ingredient = new Ingredient(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'alcohol_percentage' => $row['alcohol_percentage'],
                'description' => $row['description']
            ));
            return $ingredient;
        }
        return null;
    }
    
    public static function find_by_drink_id($id){
        $query = DB::connection()->prepare('SELECT * 
                                            FROM Ingredient
                                            INNER JOIN Ingredient_Drink
                                                ON Ingredient_Drink.ingredient_id = Ingredient.id
                                            WHERE Ingredient_Drink.drink_id = :id');
        $query->execute(array('id' => $id));
        
        $rows = $query->fetchAll();
        $ingredients = array();
        
        foreach($rows as $row) {
            $ingredients[] = new Ingredient(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'alcohol_percentage' => $row['alcohol_percentage'],
                'description' => $row['description']
            ));
        }
        return $ingredients;
    }
    
}