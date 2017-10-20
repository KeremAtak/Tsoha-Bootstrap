<?php

  require 'app/models/alcoholic.php';
  require 'app/models/drink.php';
  
  /**
    * Käyttäjän kontrolleri.
    */
  class UserController extends BaseController{

    /**
      * Metodi luo näkymän missä listataan kaikki käyttäjät.
      */
    public static function users(){
        $users = Alcoholic::all();
        
   	View::make('users.html', array('users' => $users));
    }
    
    /**
      * Metodi luo näkymän yksittäiselle käyttäjälle id:n perusteella ja tuo näkymään käyttäjän luomat
      * drinkit.
      * Jos käyttäjä on sisäänkirjautunut käyttäjä niin näkymään tulee hyperlinkki käyttäjän
      * poistamiseen.
      */
    public static function user($id){
        $user = Alcoholic::single($id);
        $drinks = Drink::find_by_user_id($id);
        
        if (Alcoholic::get_user_logged_in_id() == $id) {
            View::make('user.html', array('user' => $user, 'drinks' => $drinks,
                'delete_path' => '/tsoha/users/'.$id.'/delete', 'delete_text' => 'Poista käyttäjäsi'));
        }
        View::make('user.html', array('user' => $user, 'drinks' => $drinks));
    }
    
     /**
      * Metodi poistaa käyttäjän tietokannasta jos käyttäjä on sisäänkirjautunut käyttäjä.
      */
    public static function delete($id){
        $user = Alcoholic::single($id);
        
        if (Alcoholic::get_user_logged_in_id() == $id) {
            $user->delete();
            Redirect::to('/logout', array('message' => 'Tili poistettu!'));
        }
        Redirect::to('/users', array('message' => 'Et voi poistaa muiden tilejä!'));
    }
    
  }
