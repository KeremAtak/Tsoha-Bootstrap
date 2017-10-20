<?php

/**
  * Alcoholic on palvelun käyttäjä. Alcoholicilla on enemmän oikeuksia palvelussa kuin vierailijalla.
  */
class Alcoholic extends BaseModel{
    
    public $id, $username, $password, $validators;

    public function __construct($attributes){
        parent::__construct($attributes);
        $this->validators = array('validate_unique_name', 'validate_name_length', 'validate_name_shortness', 'validate_password_length', 'validate_password_shortness');
    }
    
    /**
      * Metodi palauttaa käyttäjät aakkosjärjestyksessä.
      */
    public static function all() {
        $query = DB::connection()->prepare('SELECT * FROM Alcoholic ORDER BY username');
        $query->execute();
        
        $rows = $query->fetchAll();
        $users = array();
        
        foreach($rows as $row) {
            
            $users[] = new Alcoholic(array(
                'id' => $row['id'],
                'username' => $row['username'],
                'password' => $row['password']
            ));
        }
        return $users;
    }
    
    /**
      * Metodi palauttaa yksittäisen käyttäjän id:n perusteella.
      */
    public static function single($id) {
        $query = DB::connection()->prepare('SELECT * FROM Alcoholic WHERE id = :id');
        $query->execute(array('id' => $id));

        $row = $query->fetch();
        
        if($row) {
            $user = new Alcoholic(array(
                'id' => $row['id'],
                'username' => $row['username'],
                'password' => $row['password']
            ));
            return $user;
        }
        return null;
    }
    
    /**
      * Metodi poistaa käyttäjän tietokannasta id:n perusteella.
      */
    public function delete(){
        $query = DB::connection()->prepare('DELETE FROM Alcoholic WHERE id = :id');
        
        $query->execute(array('id' => $this->id));
    }
    
    /**
      * Metodi tarkastaa löytyykö käyttäjää käyttäjätunnuksen ja salasanan perusteella ja palauttaa tämän
      * jos tiedot täsmäävät.
      */
    public static function authenticate($username, $password) {
        $query = DB::connection()->prepare('SELECT * FROM Alcoholic WHERE username = :username AND password = :password LIMIT 1');
        $query->execute(array('username' => $username, 'password' => $password));
        $row = $query->fetch();
        if($row){
          $user = new Alcoholic(array(
                'id' => $row['id'],
                'username' => $row['username'],
                'password' => $row['password']
            ));
            return $user;
        }else{
          return null;
        }
    }
    
    /**
      * Metodi tallentaa käyttäjän tietokantaan.
      */
    public function save(){
        $query = DB::connection()->prepare('INSERT INTO Alcoholic (username, password)
                                    VALUES (:username, :password) RETURNING id');

        $query->execute(array('username' => $this->username, 'password' => $this->password));
                
        $row = $query->fetch();

        $this->id = $row['id'];
    }
    
    /**
      * Metodi palauttaa sisäänkirjautuneen käyttäjän.
      */
    public static function get_user_logged_in(){
      if(isset($_SESSION['user'])){
        $id = $_SESSION['user'];
        $alcoholic = Alcoholic::single($id);

        return $alcoholic;
      }
      return null;
    }
    
    /**
      * Metodi palauttaa sisäänkirjautuneen käyttäjän tunnuksen.
      */
    public static function get_user_logged_in_id(){
      if(isset($_SESSION['user'])){
        $id = $_SESSION['user'];
        return $id;
      }
      return null;
    }
    
    /**
      * Metodi tarkastaa onko käyttäjä kirjautunut sisään.
      */
    public static function is_logged_in(){
    if(isset($_SESSION['user'])){
        return TRUE;
      }
      return FALSE;
    }
    
    /**
      * Metodi validoi onko käyttäjänimi uniikki.
      */
    public function validate_unique_name(){
        $query = DB::connection()->prepare('SELECT * FROM Alcoholic WHERE username = :username');
        $query->execute(array('username' => $this->username));
        
        $row = $query->fetch();
        $errors = array();
        if ($row) {
            $errors[] = 'Käyttäjänimi on varattu.';
        }
        return $errors;
    }
    
    /**
      * Metodi validoi onko käyttäjänimi liian pitkä.
      */
    public function validate_name_length(){
        return $this->validate_string_length($this->username, 30, 'Käyttäjätunnus');
    }
    
    /**
      * Metodi validoi onko käyttäjänimi liian lyhyt.
      */
    public function validate_name_shortness(){
        return $this->validate_string_length_shortness($this->username, 4, 'Käyttäjätunnus');
    }
    
    /**
      * Metodi validoi onko salasana liian pitkä.
      */
    public function validate_password_length(){
        return $this->validate_string_length($this->password, 30, 'Salasana');
    }
    
    /**
      * Metodi validoi onko salasana liian lyhyt.
      */
    public function validate_password_shortness(){
        return $this->validate_string_length_shortness($this->password, 8, 'Salasana');
    }
    
}
