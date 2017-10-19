<?php
    
  require 'app/models/drink.php';
  require 'app/models/ingredient.php';
  require 'app/models/ingredient_drink.php';
  require 'app/models/alcoholic.php';
  require 'app/models/review.php';
  
  class DrinkController extends BaseController{

    public static function drinks($word){
        $drinks = Drink::all($word);
        View::make('drinks.html', array('drinks' => $drinks));
    }
        
    public static function drink($id){
        $drink = Drink::single($id);
        $ingredients = Ingredient::find_by_drink_id($id);
        $alcoholic = Alcoholic::single($drink->alcoholic_id);
        
        
        if(Drink::user_logged_in_equals_drink_creator($id)) {
            View::make('drink.html', array('drink' => $drink, 'ingredients' => $ingredients, 
                                             'alcoholic' => $alcoholic, 'path_text' => 'Poista drinkki', 
                'update_text' => 'Päivitä drinkki', 'update_path' => '/tsoha/drinks/'.$id.'/update'));
        }
        View::make('drink.html', array('drink' => $drink, 'ingredients' => $ingredients, 'alcoholic' => $alcoholic));
    }
        
    
    public static function create_drink(){
        if(Alcoholic::is_logged_in()) {
            $ingredients = Ingredient::all();
            View::make('create.html', array('ingredients' => $ingredients));
        } else {
            Redirect::to('/login');
        }
    }
    
    public static function store(){
        $params = $_POST;
        
        $alcoholic_id = Alcoholic::get_user_logged_in_id();
        
        $oldingredients = array(Ingredient::find_by_name($params['ingredient1']),
                                Ingredient::find_by_name($params['ingredient2']),
                                Ingredient::find_by_name($params['ingredient3']),
                                Ingredient::find_by_name($params['ingredient4']),
                                Ingredient::find_by_name($params['ingredient5']),
                                Ingredient::find_by_name($params['ingredient6']));
         
        $oldvolumes = array($params['volume1'], $params['volume2'], $params['volume3'], $params['volume4'], $params['volume5'], $params['volume6']);
        $volume = 0;
                
        foreach($oldvolumes as $oldvolume) {
            if ($oldvolume != null) {
                if ($oldvolume <= 0) {
                    Redirect::to('/create', array('message' => 'Tarkasta syötteet.'));
                }
            }
        }
        
        for ($i = 0; $i <= 5; $i++) {
            if ($oldvolumes[$i] != null) {
                $volume = $volume + $oldvolumes[$i];
            } else {
                unset($oldvolumes[$i]);
                unset($oldingredients[$i]);
            }
        }
        
        $ingredients = array_values($oldingredients);
        $volumes = array_values($oldvolumes);
        
        
        $alcohol_percentage = 0;
        for ($i = 0; $i < count($volumes); $i++) {
            $alcohol_percentage =  $alcohol_percentage + $ingredients[$i]->alcohol_percentage * ($volumes[$i] / $volume);
        }
        
        $alcohol_percentage = round($alcohol_percentage, 2);
        
        $attributes = array(
            'alcoholic_id' => $alcoholic_id,
            'name' => $params['name'],
            'volume' => $volume,
            'alcohol_percentage' => $alcohol_percentage,
            'rating' => 0,
            'description' => $params['description']
        );
        
        $drink = new Drink($attributes);
        $errors = $drink->errors();

        if(count($errors) == 0){
            
          $drink->save();
          
          $drink_id = $drink->id;
        
          for ($i = 0; $i < count($volumes); $i++) {
            $ingredient_id = $ingredients[$i]->id;

            $idattributes = array(
                'ingredient_id' => $ingredient_id,
                'drink_id' => $drink_id
            );

            $ingredient_drink = new Ingredient_Drink($idattributes);
            $ingredient_drink->save();
        }
          
          Redirect::to('/drinks/' . $drink_id, array('message' => 'Drinkki luotu!'));
        } else {
          Redirect::to('/create', array('errors' => $errors, 'message' => 'Drinkin luonti epäonnistui.'));
        }
    }
    
    public static function update($id){
        $params = $_POST;
        
        $alcoholic_id = Alcoholic::get_user_logged_in_id();
        
        $oldingredients = array(Ingredient::find_by_name($params['ingredient1']),
                                Ingredient::find_by_name($params['ingredient2']),
                                Ingredient::find_by_name($params['ingredient3']),
                                Ingredient::find_by_name($params['ingredient4']),
                                Ingredient::find_by_name($params['ingredient5']),
                                Ingredient::find_by_name($params['ingredient6']));
         
        $oldvolumes = array($params['volume1'], $params['volume2'], $params['volume3'], $params['volume4'], $params['volume5'], $params['volume6']);
        $volume = 0;
                
        foreach($oldvolumes as $oldvolume) {
            if ($oldvolume != null) {
                if ($oldvolume <= 0) {
                    Redirect::to('/drinks/' . $id . '/update', array('message' => 'Tarkasta syötteet.'));
                }
            }
        }
        
        for ($i = 0; $i <= 5; $i++) {
            if ($oldvolumes[$i] != null) {
                $volume = $volume + $oldvolumes[$i];
            } else {
                unset($oldvolumes[$i]);
                unset($oldingredients[$i]);
            }
        }
        
        $ingredients = array_values($oldingredients);
        $volumes = array_values($oldvolumes);
        
        $alcohol_percentage = 0;
        for ($i = 0; $i < count($volumes); $i++) {
            $alcohol_percentage =  $alcohol_percentage + $ingredients[$i]->alcohol_percentage * ($volumes[$i] / $volume);
        }
        
        $alcohol_percentage = round($alcohol_percentage, 1);

        $attributes = array(
            'id' => $id,
            'alcoholic_id' => $alcoholic_id,
            'name' => $params['name'],
            'volume' => $volume,
            'alcohol_percentage' => $alcohol_percentage,
            'rating' => 0,
            'description' => $params['description']
        );
        
        $drink = new Drink($attributes);
        $errors = $drink->errors();

        if(count($errors) == 0){   
          $drink->update();
          
          Ingredient_Drink::remove_by_drink_id($id);
          Review::remove_by_drink_id($id);
          
          for ($i = 0; $i < count($volumes); $i++) {
            $ingredient_id = $ingredients[$i]->id;

            $idattributes = array(
                'ingredient_id' => $ingredient_id,
                'drink_id' => $id
            );

            $ingredient_drink = new Ingredient_Drink($idattributes);
            $ingredient_drink->save();
        }
          Redirect::to('/drinks/' . $id, array('message' => 'Drinkki päivitetty!'));
        } else {
          Redirect::to('/drinks/' . $id . '/update', array('errors' => $errors, 'message' => 'Drinkin päivitys epäonnistui.'));
        }
    }
    
    public static function remove($id){
        $drink = Drink::single($id);
        
        if(Drink::user_logged_in_equals_drink_creator($id)) {
            $drink->remove();
            Redirect::to('/drinks', array('message' => 'Drinkki poistettu!'));
        }
        Redirect::to('/drinks', array('message' => 'Et voi poistaa muiden drinkkejä!'));
    }
    
    public static function update_drink($id){
        $drink = Drink::single($id);
        $alcoholic = Alcoholic::get_user_logged_in();
        if($alcoholic == null) {
            Redirect::to('/login', array('message' => 'Kirjaudu sisään että pääset päivittämään drinkkisi!'));     
        } else if ($drink->alcoholic_id != $alcoholic->id) {
            Redirect::to('/drinks/'. $id.'', array('drink' => $drink, 'message' => 'Et voi päivittää muiden drinkkejä!'));
        } else {
            $ingredients = Ingredient::all();
            View::make('updatedrink.html', array('drink' => $drink, 'ingredients' => $ingredients));
        }
    }
    
    public static function update_rating($id){
        $reviews = Review::find_by_drink_id($id);
        $drink = Drink::single($id);

        foreach($reviews as $review) {
            $rating = $rating + $review->rating;
        }

        $rating = $rating / count(reviews);

        $drink->update_rating($rating);
    }
}