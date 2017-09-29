<?php

  require 'app/models/review.php';
  require 'app/models/ingredient.php';
  require 'app/models/drink.php';
  require 'app/models/alcoholic.php';

  class ReviewController extends BaseController{

    public static function reviews($id){
        $reviews = Review::find_by_drink_id($id);
        $drink = Drink::single($id);
        
        View::make('reviews.html', array('reviews' => $reviews, 'drink' => $drink));
    }
    
    public static function create_review($id){
        $drink = Drink::single($id);
        if(Alcoholic::get_user_logged_in() == NULL) {
            $reviews = Review::find_by_drink_id($id);
            View::make('reviews.html', array('reviews' => $reviews, 'drink' => $drink, 'error' => 'Kirjaudu sisään että pääset arvostelemaan drinkkiä!'));
        }
        View::make('review.html', array('drink' => $drink));
    }
    
    public static function single_review($id, $review_id){
        $drink = Drink::single($id);
        $review = Review::single($id);
        
        View::make('singlereview.html', array('review' => $review, 'drink' => $drink));
    }
    
    
    public static function store($id) {
        $params = $_POST;
        
        $rating = strval($params['rating']);
        $reviewer_object = Alcoholic::get_user_logged_in();
        
        $reviewer = $reviewer_object->username;
        $alcoholic_id = Drink::find_alcoholic_id($id);
        
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
          DrinkController::update_rating($id);
          Redirect::to('/drinks/' . $id . '/reviews', array('message' => 'Arvostelusi on lisätty.'));
        }else{
          View::make('/drinks/' . $id . '/reviews/review', array('errors' => $errors, 'message' => 'Arvostelun luonti epäonnistui.'));
        }
        
        
        Redirect::to('/drinks/' . $id . '/reviews');
    }
    
    public static function remove($id, $review_id){
        $review = Review::single($review_id);

        $review->remove();
        
        $reviews = Review::find_by_drink_id($id);
        $drink = Drink::single($id);
        
        View::make('reviews.html', array('reviews' => $reviews, 'drink' => $drink));
    }
  }