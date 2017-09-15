<?php

  class UserController extends BaseController{

    public static function users(){
        View::make('users.html');
    }
    
    public static function user(){
        View::make('user.html');
    }
  }
