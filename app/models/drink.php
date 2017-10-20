<?php

/**
  * Drinkki on palvelun keskeinen taulu. Käyttäjät lukevat reseptejä ja arvioivat niitä.
  */
class Drink extends BaseModel{
    
    public $id, $alcoholic_id, $name, $volume, $alcohol_percentage, $rating, $description, $validators;

    public function __construct($attributes){
        parent::__construct($attributes);
        $this->validators = array('validate_name', 'validate_name_not_null', 'validate_description', 'validate_volume', 'validate_creator');
    }
    
    /**
      * Metodi palauttaa kaikki drinkit. Parametri $word päättää millaisessa järjestyksessä
      * kysely palauttaa drinkit käyttäjälle.
      */
    public static function all($word) {
        $query = DB::connection()->prepare('SELECT * FROM Drink ORDER BY '. $word);
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
    
    /**
      * Metodi palauttaa yksittäisen drinkin id:n perusteella.
      */
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
    
     /**
      * Metodi palauttaa kaikki drinkit mitkä liittyvät yksittäiseen käyttäjään mikä löytyy
      * käyttäjän id:n perusteella.
      */
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
    
     /**
      * Metodi palauttaa käyttäjän id:n mihin liittyy drinkin id:llä löytyvä drinkki.
      * Jos drinkkiä ei löydy palautetaan null.
      */
    public static function find_alcoholic_id($id) {
        $query = DB::connection()->prepare('SELECT alcoholic_id FROM Drink WHERE id = :id');
        $query->execute(array('id' => $id));

        $row = $query->fetch();
        
        if($row) {
            return $row['alcoholic_id'];
        }
        return null;
    }
    
     /**
      * Metodi palauttaa drinkit liittyen ainesosaan mikä löytyy id:n perusteella.
      */
    public static function find_by_ingredient_id($id){
        $query = DB::connection()->prepare('SELECT * 
                                            FROM Drink
                                            INNER JOIN Ingredient_Drink
                                                ON Ingredient_Drink.drink_id = Drink.id
                                            WHERE Ingredient_Drink.ingredient_id = :id');
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
    
     /**
      * Metodi tarkastaa onko käyttäjä luonut drinkin.
      */
    public static function creator_equals_reviewer($id, $alcoholic_id) {
        $query = DB::connection()->prepare('SELECT * FROM Drink WHERE id = :id
                                                        AND alcoholic_id = :alcoholic_id');
        $query->execute(array('id' => $id, 'alcoholic_id' => $alcoholic_id));
        $row = $query->fetch();
        
        if($row) {
            return true;
        }
        return false;
    }
    
    /**
      * Metodi tallentaa drinkin tietokantaan.
      */
    public function save(){
        $query = DB::connection()->prepare('INSERT INTO Drink (alcoholic_id, name, volume, alcohol_percentage, rating, description)
                                    VALUES (:alcoholic_id, :name, :volume, :alcohol_percentage, :rating, :description) RETURNING id');

        $query->execute(array('alcoholic_id' => $this->alcoholic_id, 'name' => $this->name, 'volume' => $this->volume, 
            'alcohol_percentage' => $this->alcohol_percentage, 'rating' => $this->rating, 'description' => $this->description));
        $row = $query->fetch();

        $this->id = $row['id'];
    }
    
    /**
      * Metodi päivittää drinkin.
      */
    public function update() {
        $query = DB::connection()->prepare('UPDATE Drink SET name = :name, volume = :volume,
                alcohol_percentage = :alcohol_percentage, rating = :rating, description = :description WHERE id = :id');

        $query->execute(array('id' => $this->id, 'name' => $this->name, 'volume' => $this->volume, 
            'alcohol_percentage' => $this->alcohol_percentage, 'rating' => $this->rating, 'description' => $this->description));
    }
    
    /**
      * Metodi päivittää arvosanan ja päivittää täten drinkin.
      */
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
    
    /**
      * Metodi poistaa drinkin tietokannasta.
      */
    public function remove(){
        $query = DB::connection()->prepare('DELETE FROM Drink WHERE id = :id');
        
        $query->execute(array('id' => $this->id));
    }
    
    /**
      * Metodi tarkastaa onko sisäänkirjautunut käyttäjä luonut drinkin.
      */
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
    
    /**
      * Metodi validoi onko nimi liian pitkä.
      */
    public function validate_name(){
        return $this->validate_string_length($this->name, 50, 'Nimi');
    }
    
    /**
      * Metodi validoi onko nimi tyhjä.
      */
    public function validate_name_not_null(){
        return $this->validate_string_length_shortness($this->name, 0, 'Nimi');
    }
    
    /**
      * Metodi validoi onko kuvaus liian pitkä.
      */
    public function validate_description(){
        return $this->validate_string_length($this->description, 1500, 'Kuvaus');
    }
    
    /**
      * Metodi validoi onko tilavuus liian lyhyt.
      */
    public function validate_volume(){
        $errors = array();
        
        if($this->volume <= 0){
          $errors[] = 'Tilavuus on 0 tai alle.';
        }
        
        return $errors;
    }
    
    /**
      * Metodi validoi ettei vierailija ole luonut drinkkiä.
      */
    public function validate_creator(){
        $errors = array();
        
        if($this->alcoholic_id == null){
          $errors[] = 'Vierailijat eivät saa luoda drinkkejä.';
        }
        
        return $errors;
    }
}
