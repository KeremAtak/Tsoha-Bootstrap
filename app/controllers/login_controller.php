<?php

  require 'app/models/alcoholic.php';
  
  /**
    * Kirjautumisen controller.
    */
  class LoginController extends BaseController{
      
      
    /**
      * Metodi palauttaa sisäänkirjautuneen käyttäjän. Metodi palauttaa null jos sivuston kävijä
      * ei ole kirjautunut sisään.
      */  
    public static function get_user_logged_in(){
        if (isset($_SESSION['user'])) {
          $id = $_SESSION['user'];
          
          $alcoholic = Alcoholic::single($id);
          return $alcoholic;
        }

      return null;
    }

    /**
      * Metodi palauttaa näkymän kirjautumissivulle jos kävijä ei ole kirjautunut sisään.
      */
    public static function login() {
        if (!Alcoholic::is_logged_in()) {
            View::make('login.html');
        }
    }
    
    /**
      * Metodissa kirjaudutaan sisään jos käyttäjätunnus ja salasana täsmäävät.
      */
    public static function login_user(){
        $params = $_POST;
        $alcoholic = Alcoholic::authenticate($params['username'], $params['password']);

        if(!$alcoholic){
          View::make('login.html', array('message' => 'Väärä käyttäjätunnus tai salasana!'));
        } else {
          $_SESSION['user'] = $alcoholic->id;
          Redirect::to('/');
        }
    }
    
    /**
      * Metodissa kirjaudutaan ulos jos kävijä on kirjautunut sisään.
      */
    public static function logout(){
        if(Alcoholic::is_logged_in()) {
            $_SESSION['user'] = null;
            Redirect::to('/', array('message' => 'Olet kirjautunut ulos.'));
        }
    }
    
    /**
      * Metodi palauttaa näkymän rekisteröitymissivulle jos kävijä ei ole kirjautunut sisään.
      */
    public static function register() {
        if (!Alcoholic::is_logged_in()) {
            View::make('register.html');
        }
    }
    
    /**
      * Metodissa luodaan uusi käyttäjä. Syötteet validoidaan aluksi.
      */
    public static function register_new_user() {
        $params = $_POST;
        
        $alcoholic = new Alcoholic(array(
            'username' => $params['username'],
            'password' => $params['password']
        ));
        
        $errors = $alcoholic->errors();

        if(count($errors) == 0){
            $alcoholic->save();
            Redirect::to('/login');
        } else{
          Redirect::to('/register', array('errors' => $errors, 'message' => 'Käyttäjän luonti epäonnistui.'));
        }
    }
  }