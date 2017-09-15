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
  
  $routes->get('/drinks/drink', function() {
    DrinkController::drink();
  });
  
  $routes->get('/drinks/drink/reviews', function() {
    DrinkController::reviews();
  });
  
  $routes->get('/create', function() {
    DrinkController::create_drink();
  });
  
  $routes->get('/ingredients', function() {
      IngredientController::ingredients();
  });
  
  $routes->get('/ingredients/ingredient', function() {
      IngredientController::ingredient();
  });
  
  $routes->get('/users', function() {
    UserController::users();
  });
  
    $routes->get('/users/user', function() {
    UserController::user();
  });
