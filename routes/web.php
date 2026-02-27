<?php

Route::middleware('web')->group(function () {
    Route::view('/quick-order', 'rapidez-quick-order::form')->name('quick-order.form');
});
