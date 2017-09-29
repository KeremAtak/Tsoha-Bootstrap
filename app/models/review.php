<?php

class Review extends BaseModel{
    
    public $id, $alcoholic_id, $drink_id, $reviewer, $rating, $description;

    public function __construct($attributes){
        parent::__construct($attributes);
        $this->validators = array('validate_description', 'validate_reviewer', 'validate_rating');
    }
    
    public static function single($id) {
        $query = DB::connection()->prepare('SELECT * FROM Review WHERE id = :id');
        $query->execute(array('id' => $id));

        $row = $query->fetch();
        
        if($row) {
            $review = new Review(array(
                'id' => $row['id'],
                'alcoholic_id' => $row['alcoholic_id'],
                'drink_id' => $row['drink_id'],
                'reviewer' => $row['reviewer'],
                'rating' => $row['rating'],
                'description' => $row['description']
            ));
            return $review;
        }
        return null;
    }
    
    
    public static function find_by_drink_id($id){
        $query = DB::connection()->prepare('SELECT * FROM Review where drink_id = :id');
        $query->execute(array('id' => $id));
        
        $rows = $query->fetchAll();
        $reviews = array();
        
        foreach($rows as $row) {
            $reviews[] = new Ingredient(array(
                'id' => $row['id'],
                'alcoholic_id' => $row['alcoholic_id'],
                'drink_id' => $row['drink_id'],
                'reviewer' => $row['reviewer'],
                'rating' => $row['rating'],
                'description' => $row['description']
            ));
        }
        return $reviews;
    }
    
    public function save(){
        $query = DB::connection()->prepare('INSERT INTO Review (alcoholic_id, drink_id, reviewer, rating, description)
                                    VALUES (:alcoholic_id, :drink_id, :reviewer, :rating, :description) RETURNING id');

        $query->execute(array('alcoholic_id' => $this->alcoholic_id, 'drink_id' => $this->drink_id, 'reviewer' => $this->reviewer, 
            'rating' => $this->rating, 'description' => $this->description));
        
        
        $row = $query->fetch();

        $this->id = $row['id'];
    }
    
    public function remove(){
        $query = DB::connection()->prepare('DELETE FROM Review WHERE id = :id');

        $query->execute(array('id' => $this->id));
        
        $row = $query->fetch();
    }
    
    public function validate_description(){
        $errors = array();
        
        if(strlen($this->description) > 1500){
          $errors[] = 'Arvostelu on liian pitkä (max 1500 merkkiä.';
        }

        if($this->reviewer == null){
          $errors[] = 'Vierailijat eivät saa arvostella drinkkejä.';
        }
        
        if(strlen($this->description) > 1500){
          $errors[] = 'Arvostelu on liian pitkä (max 1500 merkkiä.';
        }
        return $errors;
    }
    
    public function validate_reviewer(){
        $errors = array();
        
        if($this->reviewer == null){
          $errors[] = 'Vierailijat eivät saa arvostella drinkkejä.';
        }
        
        return $errors;
    }
    
    public function validate_rating(){
        $errors = array();
        
        if (!isset($_POST['rating'])) {
          $errors[] = 'Arvostelulta puuttuu arvosana.';
        }
        return $errors;
    }
}
