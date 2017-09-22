<?php
    
  require 'app/models/drink.php';
  require 'app/models/ingredient.php';
  require 'app/models/alcoholic.php';
  
  class DrinkController extends BaseController{

    public static function drinks(){
        $drinks = Drink::all();
        
        View::make('drinks.html', array('drinks' => $drinks));
    }
    
    public static function drink($id){
        $drink = Drink::single($id);
        $ingredients = Ingredient::find_by_drink_id($id);
        $alcoholic_id = Drink::find_alcoholic_id($id);
        $user = Alcoholic::single($alcoholic_id);
        
        View::make('drink.html', array('drink' => $drink, 'ingredients' => $ingredients, 'user' => $user));
    }
        
    
    public static function create_drink(){
        View::make('create.html');
    }
  }