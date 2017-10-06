<?php

class Review extends BaseModel{
    
    public $id, $alcoholic_id, $drink_id, $reviewer, $rating, $description, $validators;

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
        $query = DB::connection()->prepare('SELECT * FROM Review where drink_id = :id ORDER BY rating');
        $query->execute(array('id' => $id));
        
        $rows = $query->fetchAll();
        $reviews = array();
        
        foreach($rows as $row) {
            $reviews[] = new Review(array(
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
    
    public static function user_logged_in_equals_reviewer($review_id){
        if(Alcoholic::get_user_logged_in() != null) {
            $user = Alcoholic::get_user_logged_in();
            $review = Review::single($review_id);
            if ($review->reviewer == $user->username) {
                return TRUE;
            }
        }
        return FALSE;
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
    }
    
    public function validate_description(){
        return $this->validate_string_length($this->description, 1500);
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
