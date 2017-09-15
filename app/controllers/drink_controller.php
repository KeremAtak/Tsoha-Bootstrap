<?php

  class DrinkController extends BaseController{

    public static function drinks(){
        View::make('drinks.html');
    }
    
    public static function drink(){
        View::make('drink.html');
    }
        
    public static function reviews(){
        View::make('reviews.html');
    }
    
    public static function create_drink(){
        View::make('create.html');
    }
  }