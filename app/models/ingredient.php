<?php

/**
  * Ingredient on ainesosa joilla käyttäjä luo drinkin. Käyttäjät eivät voi luoda näitä.
  */
class Ingredient extends BaseModel{
    
    public $id, $name, $alcohol_percentage, $description;

    public function __construct($attributes){
        parent::__construct($attributes);
    }
    
    /**
      * Metodi palauttaa kaikki ainesosat. Parametri $word päättää millaisessa järjestyksessä
      * kysely palauttaa ainesosat käyttäjälle.
      */
    public static function all($word) {
        $query = DB::connection()->prepare('SELECT * FROM Ingredient ORDER BY ' . $word);
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
   
    /**
      * Metodi palauttaa yksittäisen ainesosan id:n perusteella.
      */
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
    
     /**
      * Metodi palauttaa yksittäisen ainesosan nimen perusteella.
      */
    public static function find_by_name($name) {
        $query = DB::connection()->prepare('SELECT * FROM Ingredient WHERE name = :name');
        $query->execute(array('name' => $name));

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
    
     /**
      * Metodi palauttaa kaikki drinkin id:n liittyvät ainesosat.
      */
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