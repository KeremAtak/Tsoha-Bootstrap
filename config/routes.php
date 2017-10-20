<?php
  
  $routes->get('/', function() {
    IndexController::index();
  });

  $routes->get('/login', function() {
    LoginController::login();
  });
  
  $routes->post('/login', function() {
    LoginController::login_user();
  });
  
  $routes->get('/logout', function() {
    LoginController::logout();
  });
  
  $routes->get('/register', function() {
    LoginController::register();
  });
  
  $routes->post('/register', function() {
    LoginController::register_new_user();
  });
  
  $routes->get('/drinks', function() {
    DrinkController::drinks('name ASC');
  });
  
  $routes->get('/drinks/alcohol', function() {
    DrinkController::drinks('alcohol_percentage DESC');
  });
  
  $routes->get('/drinks/rating', function() {
    DrinkController::drinks('rating DESC');
  });
  
  $routes->get('/drinks/volume', function() {
    DrinkController::drinks('volume DESC');
  });
  
  $routes->get('/drinks/:id', function($id) {
    DrinkController::drink($id);
  });
  
  $routes->get('/drinks/:id/update', function($id) {
    DrinkController::update_drink($id);
  });
  
   $routes->post('/drinks/:id/update', function($id) {
    DrinkController::update($id);
  });
  
  
  $routes->get('/drinks/:id/remove', function($id) {
    DrinkController::remove($id);
  });
  
  $routes->get('/drinks/:id/reviews', function($id) {
    ReviewController::reviews($id);
  });
  
   $routes->get('/drinks/:id/reviews/review', function($id) {
      ReviewController::create_review($id);
   });
   
   $routes->get('/drinks/:id/reviews/update', function($id) {
      ReviewController::update_review($id);
   });
   
   $routes->post('/drinks/:id/reviews/update', function($id) {
      ReviewController::update($id);
   });
   
   $routes->get('/drinks/:id/reviews/:review_id', function($id, $review_id) {
      ReviewController::single_review($id, $review_id);
   });
  
   $routes->get('/drinks/:id/reviews/:review_id/remove', function($id, $review_id){
      ReviewController::remove($id, $review_id);
   });
  $routes->post('/drinks/:id/reviews/review', function($id) {
    ReviewController::store($id);
  });
  
  $routes->get('/create', function() {
    DrinkController::create_drink();
  });
  
  $routes->post('/create', function() {
    DrinkController::store();
  });
  
  $routes->get('/ingredients', function() {
      IngredientController::ingredients('name ASC');
  });
  
  $routes->get('/ingredients/alcohol', function() {
      IngredientController::ingredients('alcohol_percentage DESC');
  });
  
  $routes->get('/ingredients/:id', function($id) {
      IngredientController::ingredient($id);
  });
  
  $routes->get('/users', function() {
    UserController::users();
  });
  
  $routes->get('/users/:id', function($id) {
    UserController::user($id);
  });
  
  $routes->get('/users/:id/delete', function($id) {
    UserController::delete($id);
  });
