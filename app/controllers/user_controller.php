<?php

  require 'app/models/alcoholic.php';
  require 'app/models/drink.php';
  
  class UserController extends BaseController{

    public static function users(){
        $users = Alcoholic::all();
        
   	View::make('users.html', array('users' => $users));
    }
    
    public static function user($id){
        $user = Alcoholic::single($id);
        $drinks = Drink::find_by_user_id($id);
        
        View::make('user.html', array('user' => $user, 'drinks' => $drinks));
    }
    
    
  }
