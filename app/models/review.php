<?php

 /**
   * Rating on drinkin arvostelu joita käyttäjät luovat.
   */
class Review extends BaseModel{
    
    public $id, $alcoholic_id, $drink_id, $reviewer, $rating, $description, $validators;

    public function __construct($attributes){
        parent::__construct($attributes);
        $this->validators = array('validate_description');
    }
    
    /**
      * Metodi palauttaa yksittäisen arvostelun id:n perusteella.
      */
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

    /**
      * Metodi palauttaa kaikki arvostelut liittyen drinkin id:n järjestäen ne arvosanojen mukaan.
      */
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
    
    /**
      * Metodi palauttaa drinkin arvostelijan.
      */
    public static function find_reviewer_by_id($id){
        $query = DB::connection()->prepare('SELECT reviewer FROM Review where id = :id');
        $query->execute(array('id' => $id));
        
        $row = $query->fetch();
        
        return $row['reviewer'];
    }
    
    /**
      * Metodi tarkastaa onko sisäänkirjaantunut käyttäjä arostelijan luoja.
      */
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
    
    /**
      * Metodi palauttaa arvostelijan arvostelun mikä liittyy drinkkiin.
      */
    public static function find_users_review($drink_id, $reviewer){
        $query = DB::connection()->prepare('SELECT * FROM Review WHERE drink_id = :drink_id
                                                AND reviewer = :reviewer');
        $query->execute(array('drink_id' => $drink_id, 'reviewer' => $reviewer));
        
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
    
    /**
      * Metodi tallentaa arvostelun tietokantaan.
      */
    public function save(){
        $query = DB::connection()->prepare('INSERT INTO Review (alcoholic_id, drink_id, reviewer, rating, description)
                                    VALUES (:alcoholic_id, :drink_id, :reviewer, :rating, :description) RETURNING id');

        $query->execute(array('alcoholic_id' => $this->alcoholic_id, 'drink_id' => $this->drink_id, 'reviewer' => $this->reviewer, 
            'rating' => $this->rating, 'description' => $this->description));
        
        
        $row = $query->fetch();

        $this->id = $row['id'];
    }
    
    /**
      * Metodi päivittää arvostelun.
      */
    public function update(){
        $query = DB::connection()->prepare('UPDATE Review SET rating = :rating, description = :description WHERE id = :id');

        $query->execute(array('id' => $this->id, 'rating' => $this->rating, 'description' => $this->description));
    }
    
    /**
      * Metodi poistaa arvostelun.
      */
    public function remove(){
        $query = DB::connection()->prepare('DELETE FROM Review WHERE id = :id');
        $query->execute(array('id' => $this->id));
    }
    
    public static function remove_by_drink_id($drink_id){
        $query = DB::connection()->prepare('DELETE FROM Review WHERE drink_id = :drink_id');
        $query->execute(array('drink_id' => $drink_id));
    }
    
    /**
      * Metodi validoi onko kuvaus liian pitkä.
      */
    public function validate_description(){
        return $this->validate_string_length($this->description, 1500, 'Kuvaus');
    }
    
}
