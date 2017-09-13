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
