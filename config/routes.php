<?php

  $routes->get('/', function() {
    IndexController::index();
  });

  $routes->get('/login', function() {
    IndexController::login();
  });
  
  $routes->get('/register', function() {
    IndexController::register();
  });
  
  $routes->get('/drinks', function() {
    DrinkController::drinks();
  });
  
  $routes->get('/drinks/:id', function($id) {
    DrinkController::drink($id);
  });
  
  $routes->get('/drinks/:id/reviews', function($id) {
    ReviewController::reviews($id);
  });
  
  $routes->get('/create', function() {
    DrinkController::create_drink();
  });
  
  $routes->get('/ingredients', function() {
      IngredientController::ingredients();
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
