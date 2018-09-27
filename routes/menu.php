<?php

Route::get('/ati', 'AutorizacionController@showATI')->name('Ati');
Route::post('/ati', 'AutorizacionController@postATI');
Route::get('/imprimePDF_ATI/{num_cta}',[
    'uses'=> 'AutorizacionController@PdfAlumno',
    'as'=> 'imprimePDF_ATI',
  ]);
