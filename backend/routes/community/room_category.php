<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Community\RoomSubCategoryController;



Route::apiResource('/room-sub-category', RoomSubCategoryController::class);