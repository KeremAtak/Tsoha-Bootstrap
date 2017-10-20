<?php
    
  require 'app/models/drink.php';
  require 'app/models/ingredient.php';
  require 'app/models/ingredient_drink.php';
  require 'app/models/alcoholic.php';
  require 'app/models/review.php';
  
  /**
    * Drinkkien controller.
    */
  class DrinkController extends BaseController{
      
    /**
      * Metodi luo näkymän missä listataan kaikki drinkit. Parametri $word päättää millaisessa järjestyksessä
      * kysely palauttaa drinkit käyttäjälle.
      */
    public static function drinks($word){
        $drinks = Drink::all($word);
        View::make('drinks.html', array('drinks' => $drinks));
    }
     
    /**
      * Metodi luo näkymän yksittäiselle drinkille id:n perusteella ja tuo näkymään drinkin luojan ja 
      * ainekset.
      * Jos drinkki on sisäänkirjautuneen käyttäjän luoma niin näkymään tulee hyperlinkit drinkin
      * päivittämiseen ja poistamiseen. 
      */
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
        
    /**
      * Metodi luo näkymän missä drinkki luodaan. Metodi aluksi tarkastaa onko käyttäjä kirjautunut sisään.
      */
    public static function create_drink(){
        if(Alcoholic::is_logged_in()) {
            $ingredients = Ingredient::all('name ASC');
            View::make('create.html', array('ingredients' => $ingredients));
        } else {
            Redirect::to('/login');
        }
    }
    
    /**
      * Metodi tallentaa drinkin tietokantaan jos virheitä ei ilmene.
      */
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
        
        DrinkController::ingredient_not_included_twice($ingredients, '/create');
        
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
    
    /**
      * Metodi päivittää drinkin jos virheitä ei esiinny. Drinkkiin liittyvät ainesosat ja arvostelut
      * poistetaan.
      */
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
        
        DrinkController::ingredient_not_included_twice($ingredients, '/drinks/' . $id .'/update');

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
    
    /**
      * Metodi poistaa drinkin tietokannasta jos sisäänkirjautunut käyttäjä on drinkin luoja.
      */
    public static function remove($id){
        $drink = Drink::single($id);
        
        if(Drink::user_logged_in_equals_drink_creator($id)) {
            $drink->remove();
            Redirect::to('/drinks', array('message' => 'Drinkki poistettu!'));
        }
        Redirect::to('/drinks', array('message' => 'Et voi poistaa muiden drinkkejä!'));
    }
    
    /**
      * Metodi luo näkymän missä drinkki poistetaan jos sisäänkirjautunut käyttäjä on drinkin luoja.
      */
    public static function update_drink($id){
        $drink = Drink::single($id);
        $alcoholic = Alcoholic::get_user_logged_in();
        if($alcoholic == null) {
            Redirect::to('/login', array('message' => 'Kirjaudu sisään että pääset päivittämään drinkkisi!'));     
        } else if ($drink->alcoholic_id != $alcoholic->id) {
            Redirect::to('/drinks/'. $id.'', array('drink' => $drink, 'message' => 'Et voi päivittää muiden drinkkejä!'));
        } else {
            $ingredients = Ingredient::all('name ASC');
            View::make('updatedrink.html', array('drink' => $drink, 'ingredients' => $ingredients));
        }
    }
    
    /**
      * Metodi päivittää drinkin kokonaisarvosanan.
      */
    public static function update_rating($id){
        $reviews = Review::find_by_drink_id($id);
        $drink = Drink::single($id);

        foreach($reviews as $review) {
            $rating = $rating + $review->rating;
        }

        $rating = $rating / count(reviews);

        $drink->update_rating($rating);
    }
    
    /**
      * Metodi tarkastaa että samaa ainesosaa esiintyy drinkissä vain kerran.
      */
    public static function ingredient_not_included_twice($ingredients, $path) {
        for ($i = 0; $i < count($ingredients); $i++) {
            for ($j = 0; $j < count($ingredients); $j++) {
                if ($j != $i) {
                    if ($ingredients[$i] == $ingredients[$j]) {
                        Redirect::to($path, array('message' => 'Et voi lisätä samaa ainesosaa useasti.'));
                    }
                }
            }
        }
    }
}