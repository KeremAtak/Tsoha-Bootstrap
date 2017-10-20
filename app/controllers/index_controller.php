
<?php
  require 'app/models/alcoholic.php';
  
  /**
    * IndexController käsittelee indeksisivua.
    */
  class IndexController extends BaseController{
      
    /**
      * Metodi luo indeksisivun näkymään ja tuo näkymään erilaisia hyperlinkkejä riippuen
      * siitä onko käyttäjä sisäänkirjautunut vai ei.
      */
    public static function index(){
        $alcoholic = Alcoholic::get_user_logged_in();
        
        if(Alcoholic::is_logged_in()) {
            $login_text = 'Kirjaudu ulos';
            $login_path = '/tsoha/logout';
            
            $alcoholic = Alcoholic::get_user_logged_in();
            $id = $_SESSION['user'];
            
            $register_text = "Profiili";
            $register_path = "/tsoha/users/". $id;
            
            $header = 'Kirjautunut käyttäjänä: '. $alcoholic->username;
        } else {
            $login_text = 'Kirjaudu sisään';
            $login_path = '/tsoha/login';
            
            $register_text = "Rekisteröidy palveluun";
            $register_path = "/tsoha/register";
            
            $header = 'Tervetuloa palveluun, vierailija.';
        }
        
        View::make('index.html', array('login_path' => $login_path, 'login_text' => $login_text, 
                    'register_text' => $register_text, 'register_path' => $register_path, 'header' => $header));
    }
  }
