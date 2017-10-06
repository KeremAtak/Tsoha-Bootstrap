<?php

  require 'app/models/alcoholic.php';
  class LoginController extends BaseController{
      
    public static function get_user_logged_in(){
        if (isset($_SESSION['user'])) {
          $id = $_SESSION['user'];
          
          $alcoholic = Alcoholic::single($id);
          return $alcoholic;
        }

    return null;
  }

    public static function login() {
        if (!Alcoholic::is_logged_in()) {
            View::make('login.html');
        }
    }
    
    public static function login_user(){
    $params = $_POST;

    $alcoholic = Alcoholic::authenticate($params['username'], $params['password']);

    if(!$alcoholic){
        View::make('login.html', array('error' => 'Väärä käyttäjätunnus tai salasana!', 'username' => $params['username']));
    } else {
        $_SESSION['user'] = $alcoholic->id;
        Redirect::to('/');
      }
    }
    
    public static function logout(){
        if(Alcoholic::is_logged_in()) {
            $_SESSION['user'] = null;
            Redirect::to('/', array('message' => 'Olet kirjautunut ulos.'));
        }
    }
      
    public static function register() {
        if (!Alcoholic::is_logged_in()) {
            View::make('register.html');
        }
    }
      
    public static function register_new_user() {
        $params = $_POST;
        
        $alcoholic = new Alcoholic(array(
            'username' => $params['username'],
            'password' => $params['password']
        ));
        
        $alcoholic->save();

        Redirect::to('/login');
    }
    
  }