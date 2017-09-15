<?php

  class IngredientController extends BaseController{

    public static function ingredients(){
   	  View::make('ingredients.html');
    }
    
    public static function ingredient(){
   	  View::make('ingredient.html');
    }
    
  }