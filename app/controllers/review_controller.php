<?php

  require 'app/models/review.php';
  class ReviewController extends BaseController{

    
    public static function reviews($id){
        $reviews = Review::find_by_drink_id($id);
        View::make('reviews.html', array('reviews' => $reviews));
    }
    
  }