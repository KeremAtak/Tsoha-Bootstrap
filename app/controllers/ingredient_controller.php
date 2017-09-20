<?php

  require 'app/models/ingredient.php';
  class IngredientController extends BaseController{

    public static function ingredients(){
        $ingredients = Ingredient::all();
        
   	View::make('ingredients.html', array('ingredients' => $ingredients));
    }
    
    public static function ingredient($id){
        $ingredient = Ingredient::single($id);
        
        View::make('ingredient.html', array('ingredient' => $ingredient));
    }
    
  }