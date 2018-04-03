<?php

use Bonfim\Component\Routing\Route;

// Get
Route::get('/', 'HomeController::index');
Route::get('/plano-de-estudo', 'PlanoDeEstudoController::index');
Route::get('/plano-de-estudo-2', 'PlanoDeEstudo2Controller::index');
Route::get('/historico', 'HistoricoController::index');
Route::get('/logout', 'AuthController::logout');

// Post
Route::post('/login', 'AuthController::login');
