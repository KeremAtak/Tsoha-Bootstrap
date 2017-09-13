<?php

  class IndexController extends BaseController{

    public static function index(){
   	  View::make('index.html');
    }
    
    public static function login(){
   	  View::make('login.html');
    }
    
    public static function register(){
   	  View::make('register.html');
    }

    public static function sandbox(){
      // Testaa koodiasi täällä
      echo 'Hello World!';
    }
  }
