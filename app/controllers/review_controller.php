<?php

  require 'app/models/review.php';
  require 'app/models/ingredient.php';
  require 'app/models/drink.php';
  require 'app/models/alcoholic.php';

  /**
    * Arvostelujen controller.
    */
  class ReviewController extends BaseController{

    /**
      * Metodi luo näkymän missä listataan kaikki arvostelut liittyen tiettyyn drinkkiin.
      * Jos käyttäjä on kirjautunut eikä ole drinkin luoja, hänelle annetaan hyperlinkit joko arvion
      * luomiseen (jos hän ei ole luonut arviota) tai vastaavasti arvion päivittämiseen.
      */
    public static function reviews($id){
        $reviews = Review::find_by_drink_id($id);
        $drink = Drink::single($id);
        $review_text = '';
        $review_path = '';
        
        if (Alcoholic::is_logged_in()) {
            $alcoholic = Alcoholic::get_user_logged_in();
            if(Review::find_users_review($id, $alcoholic->username) != null) {
                $review_text = 'Päivitä arviosi';
                $review_path = '/tsoha/drinks/'.$id.'/reviews/update';
            } else {
                $review_text = 'Luo arvio';
                $review_path = '/tsoha/drinks/'.$id.'/reviews/review';
            }
        }
        View::make('reviews.html', array('reviews' => $reviews, 'drink' => $drink, 'review_path' => $review_path, 'review_text' => $review_text));
    }
    
    /**
      * Metodi luo näkymän missä luodaan arvio drinkille. Jos käyttäjä ei ole kirjautunut sisään hänet ohjataan
      * kirjautumissivulle, päivityssivulle jos arvio on käyttäjän toimesta luotu tai takaisin arvostelujen
      * listaukseen jos käyttäjä on drinkin luoja.
      */
    public static function create_review($id){
        $drink = Drink::single($id);
        $alcoholic = Alcoholic::get_user_logged_in();
        $reviews = Review::find_by_drink_id($id);
        if(Alcoholic::get_user_logged_in() == NULL) {
            Redirect::to('/login', array('message' => 'Kirjaudu sisään että pääset arvostelemaan drinkkiä!'));
        } else if (Review::find_users_review($id, $alcoholic->username) != null){
            $review = Review::find_users_review($id, $alcoholic->username);
            Redirect::to('/drinks/'. $drink->id. '/reviews/update', array('drink' => $drink, 'review' => $review, 'message' => 'Olet jo luonut arvostelun drinkille. Voit päivittää sen tällä sivulla.'));
        } else if (Drink::creator_equals_reviewer($id, $alcoholic->id)){    
            Redirect::to('/drinks/'. $drink->id. '/reviews', array('reviews' => $reviews, 'drink' => $drink, 'message' => 'Et voi luoda arviota omalle drinkille!'));
        } else {
            View::make('review.html', array('drink' => $drink));
        }
    }
    
    /**
      * Metodi luo näkymän missä päivitetään drinkin arvostelu. Jos käyttäjä ei ole kirjautunut sisään hänet ohjataan
      * kirjautumissivulle, luomissivulle jos arviota ei ole käyttäjän toimesta luotu tai takaisin arvostelujen
      * listaukseen jos käyttäjä on drinkin luoja.
      */
    public static function update_review($id) {
        $drink = Drink::single($id);
        $alcoholic = Alcoholic::get_user_logged_in();
        if($alcoholic == null) {
            Redirect::to('/login', array('message' => 'Kirjaudu sisään että pääset päivittämään arvostelusi!'));
        }
        $review = Review::find_users_review($drink->id, $alcoholic->username);
        if ($review == null){
            Redirect::to('/drinks/'. $drink->id. '/reviews/review', array('drink' => $drink, 'message' => 'Et ole luonut arviota, joten et voi päivittää sitä.'));
        } else {
            View::make('updatereview.html', array('drink' => $drink, 'review' => $review));
        }
    }
    /**
      * Metodi luo näkymän yksittäiselle arvostelulle id:n perusteella ja tuo näkymään arvosteluun liittyvän
      * drinkin.
      * Jos arvostelu on sisäänkirjautuneen käyttäjän luoma niin näkymään tulee hyperlinkki arvosteluun
      * poistamiseen.
      */
    public static function single_review($id, $review_id){
        $drink = Drink::single($id);
        $review = Review::single($review_id);
        if (Review::user_logged_in_equals_reviewer($review_id)) {
            View::make('singlereview.html', array('review' => $review, 'drink' => $drink, 'path_text' => 'Poista arvostelu'));
        }
        View::make('singlereview.html', array('review' => $review, 'drink' => $drink));
    }
    
    /**
      * Metodi tallentaa arvostelun tietokantaan jos virheitä ei esiinny. Tällöin drinkin arvosana päivittyy.
      */
    public static function store($id) {
        $params = $_POST;
        
        $rating = strval($params['rating']);
        $alcoholic = Alcoholic::get_user_logged_in();
        
        $drink = Drink::single($id);
        $alcoholic_id = $alcoholic->id;
        $reviewer = $alcoholic->username;
        
        $attributes = array(
            'alcoholic_id' => $alcoholic_id,
            'drink_id' => $id,
            'reviewer' => $reviewer,
            'rating' => $rating,
            'description' => $params['description']
        );
        
        $review = new Review($attributes);
        $errors = $review->errors();

        if(count($errors) == 0){
          $review->save();
          $drink->update_rating();
          
          Redirect::to('/drinks/' . $id . '/reviews', array('message' => 'Arvostelusi on lisätty.'));
        }else{
          Redirect::to('/drinks/' . $id . '/reviews/review', array('errors' => $errors, 'message' => 'Arvostelun luonti epäonnistui.'));
        }
    }
    
    /**
      * Metodi päivittää arvostelun jos virheitä ei esiinny. Tällöin drinkin arvosana päivittyy.
      */
    public static function update($drink_id) {
        $params = $_POST;
        
        $rating = strval($params['rating']);
        $alcoholic = Alcoholic::get_user_logged_in();
        $drink = Drink::single($drink_id);
        $alcoholic_id = $alcoholic->id;
        $reviewer = $alcoholic->username;
        $currentreview = Review::find_users_review($drink->id, $alcoholic->username);
        $id = $currentreview->id;
        
        $attributes = array(
            'id' => $id,
            'alcoholic_id' => $alcoholic_id,
            'drink_id' => $drink_id,
            'reviewer' => $reviewer,
            'rating' => $rating,
            'description' => $params['description']
        );
        
        $review = new Review($attributes);
        $errors = $review->errors();

        if(count($errors) == 0){
          $review->update();
          $drink->update_rating();
          
          Redirect::to('/drinks/' . $drink->id . '/reviews', array('message' => 'Arvostelusi on päivitetty.'));
        }else{
          Redirect::to('/drinks/' . $drink->id . '/reviews/review', array('errors' => $errors, 'message' => 'Arvostelun päivitys epäonnistui.'));
        }
    }
    
    /**
      * Metodi poistaa arvostelun tietokannasta jos sisäänkirjautunut käyttäjä on arvostelun luoja.
      * Tällöin drinkin arvosana päivittyy. 
      */
    public static function remove($id, $review_id){
        $review = Review::single($review_id);
        $drink = Drink::single($id);
        
        if(Review::user_logged_in_equals_reviewer($review_id)) {
            $review->remove();
            $drink->update_rating();
        }
        
        $reviews = Review::find_by_drink_id($id);
        Redirect::to('/drinks/' . $id . '/reviews', array('reviews' => $reviews, 'drink' => $drink));
    }
  }