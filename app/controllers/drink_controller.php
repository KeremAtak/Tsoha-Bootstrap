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
        $ingredients = Ingredient::all();
        View::make('create.html', array('ingredients' => $ingredients));
    }
    
    public static function store(){
        $params = $_POST;
        
        $alcoholic_id = Alcoholic::get_user_logged_in_id();
        $ingredient1 = $params['ingredient1'];
        $ingredient2 = $params['ingredient2'];
        $ingredient3 = $params['ingredient3'];
        
        
        $volume = $params['volume1'] + $params['volume2'] + $params['volume3'];
        $alcohol_percentage_1 = $ingredient1->alcohol_percentage / 100 * ($params['volume1'] / $volume);
        $alcohol_percentage_2 = $ingredient2->alcohol_percentage / 100 * ($params['volume2'] / $volume);
        $alcohol_percentage_3 = $ingredient1->alcohol_percentage / 100 * ($params['volume3'] / $volume);
        
        $alcohol_percentage = $alcohol_percentage_1 + $alcohol_percentage_2 + $alcohol_percentage_3;
        
        $drink = new Drink(array(
            'alcoholic_id' => $alcoholic_id,
            'name' => $params['name'],
            'volume' => $volume,
            'alcohol_percentage' => $alcohol_percentage,
            'rating' => $rating,
            'description' => $params['description']
        ));
        
        $drink>save();
        
        $drink_id = $drink->id;
        
        $ingredient1_id = $ingredient1->id;
        $ingredient2_id = $ingredient2->id;
        $ingredient3_id = $ingredient3->id;
        
        $ingredient_drink_1 = new Ingredient_Drink(array(
           'ingredient_id' => $ingredient1_id,
           'drink_id' => $drink_id
        ));
        
        $ingredient_drink_2 = new Ingredient_Drink(array(
           'ingredient_id' => $ingredient2_id,
           'drink_id' => $drink_id
        ));
        
        $ingredient_drink_3 = new Ingredient_Drink(array(
           'ingredient_id' => $ingredient3_id,
           'drink_id' => $drink_id
        ));
        
        $ingredient_drink_1->save();
        $ingredient_drink_2->save();
        $ingredient_drink_3->save();
        
        Redirect::to('/drinks/' . $drink_id);
    }
    
    public static function update_rating($id){
    
    $reviews = Review::find_by_drink_id($id);
    
    foreach($reviews as $review) {
        $rating = $rating + $review->rating;
    }
    
    $rating = $rating / count(reviews);
    
    $attributes = array(
      'id' => $id,
      'rating' => $rating,
    );
        
    $drink = new Drink($attributes);
    
    $drink>update();
  }
    
  }