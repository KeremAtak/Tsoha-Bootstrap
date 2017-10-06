<?php

class Drink extends BaseModel{
    
    public $id, $alcoholic_id, $name, $volume, $alcohol_percentage, $rating, $description, $validators;

    public function __construct($attributes){
        parent::__construct($attributes);
        $this->validators = array('validate_name', 'validate_name_not_null', 'validate_description', 'validate_volume', 'validate_creator');
    }
    
    public static function all() {
        $query = DB::connection()->prepare('SELECT * FROM Drink ORDER BY name');
        $query->execute();
        
        $rows = $query->fetchAll();
        $drinks = array();
        
        foreach($rows as $row) {
            
            $drinks[] = new Drink(array(
                'id' => $row['id'],
                'alcoholic_id' => $row['alcoholic_id'],
                'name' => $row['name'],
                'volume' => $row['volume'],
                'alcohol_percentage' => $row['alcohol_percentage'],
                'rating' => $row['rating'],
                'description' => $row['description']
            ));
        }
        return $drinks;
    }
    
    public static function single($id) {
        $query = DB::connection()->prepare('SELECT * FROM Drink WHERE id = :id');
        $query->execute(array('id' => $id));

        $row = $query->fetch();
        
        if($row) {
            $drink = new Drink(array(
                'id' => $row['id'],
                'alcoholic_id' => $row['alcoholic_id'],
                'name' => $row['name'],
                'volume' => $row['volume'],
                'alcohol_percentage' => $row['alcohol_percentage'],
                'rating' => $row['rating'],
                'description' => $row['description']
            ));
            return $drink;
        }
        return null;
    }
    
    public static function find_by_user_id($id) {
        $query = DB::connection()->prepare('SELECT * FROM Drink WHERE alcoholic_id = :id');
        $query->execute(array('id' => $id));
        
        $rows = $query->fetchAll();
        $drinks = array();
        
        foreach($rows as $row) {
            
            $drinks[] = new Drink(array(
                'id' => $row['id'],
                'alcoholic_id' => $row['alcoholic_id'],
                'name' => $row['name'],
                'volume' => $row['volume'],
                'alcohol_percentage' => $row['alcohol_percentage'],
                'rating' => $row['rating'],
                'description' => $row['description']
            ));
        }
        return $drinks;
    }
    
    public static function find_alcoholic_id($id) {
        $query = DB::connection()->prepare('SELECT alcoholic_id FROM Drink WHERE id = :id');
        $query->execute(array('id' => $id));

        $row = $query->fetch();
        
        if($row) {
            return $row['alcoholic_id'];
        }
        return null;
    }
    
    public function save(){
        $query = DB::connection()->prepare('INSERT INTO Drink (alcoholic_id, name, volume, alcohol_percentage, rating, description)
                                    VALUES (:alcoholic_id, :name, :volume, :alcohol_percentage, :rating, :description) RETURNING id');

        $query->execute(array('alcoholic_id' => $this->alcoholic_id, 'name' => $this->name, 'volume' => $this->volume, 
            'alcohol_percentage' => $this->alcohol_percentage, 'rating' => $this->rating, 'description' => $this->description));
        $row = $query->fetch();

        $this->id = $row['id'];
    }
    
    public function update(){
        $query = DB::connection()->prepare('UPDATE Drink (alcoholic_id, name, volume, alcohol_percentage, rating, description)
                                    VALUES (:alcoholic_id, :name, :volume, :alcohol_percentage, :rating, :description) RETURNING id');

        $query->execute(array('alcoholic_id' => $this->alcoholic_id, 'name' => $this->name, 'volume' => $this->volume, 
            'alcohol_percentage' => $this->alcohol_percentage, 'rating' => $this->rating, 'description' => $this->description));
        $row = $query->fetch();

        $this->id = $row['id'];
    }
    
    public function update_rating(){
        $reviews = Review::find_by_drink_id($this->id);
                  
        $rating = 0;

        foreach($reviews as $review) {
          $rating = $rating + $review->rating;
        }
        
        $query = DB::connection()->prepare('UPDATE Drink SET rating = :rating WHERE id = :id');
        
        if ($rating == 0) {
            $query->execute(array('rating' => 0, 'id' => $this->id));
        } else {
            $finalrating = $rating / count($reviews);
          
            $query->execute(array('rating' => round($finalrating, 2), 'id' => $this->id));   
        }
    }
    
    public function remove(){
        $query = DB::connection()->prepare('DELETE FROM Drink WHERE id = :id');
        
        $query->execute(array('id' => $this->id));
    }
    
    public static function user_logged_in_equals_drink_creator($id){
        if(Alcoholic::get_user_logged_in() != null) {
            $user = Alcoholic::get_user_logged_in();
            $drink = Drink::single($id);
            $drink_creator = Alcoholic::single($drink->alcoholic_id);
            
            if ($drink_creator->username == $user->username) {
                return TRUE;
            }
        }
        return FALSE;
    }
    
    public function validate_name(){
        return $this->validate_string_length($this->name, 50);
    }
    
     public function validate_name_not_null(){
        return $this->string_not_null($this->name, 'Nimi');
    }
    
    public function validate_description(){
        return $this->validate_string_length($this->description, 500);
    }
    
    public function validate_volume(){
        $errors = array();
        
        if($this->volume <= 0){
          $errors[] = 'Tilavuus on 0 tai alle.';
        }
        
        return $errors;
    }
    
    public function validate_creator(){
        $errors = array();
        
        if($this->alcoholic_id == null){
          $errors[] = 'Vierailijat eivät saa luoda drinkkejä.';
        }
        
        return $errors;
    }
}
