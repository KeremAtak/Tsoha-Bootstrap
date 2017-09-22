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
    
    public static function store($id) {
        $params = $_POST;
        
        $reviewer = 'Pate';
        
        $review = new Review(array(
            'alcoholic_id' => 2,
            'drink_id' => $id,
            'reviewer' => $reviewer,
            'rating' => $params['rating'],
            'description' => $params['description']
        ));
        
        $review->save();

        Redirect::to('/drinks/' . $id . '/reviews');

    }
  }