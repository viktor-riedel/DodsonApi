<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function() {
   Route::post('/login', [AuthController::class, 'login'])
       ->middleware('throttle:api');
   Route::post('/forgot', [AuthController::class, 'forgetPassword'])
       ->middleware('throttle:api');
   Route::post('/restore', [AuthController::class, 'restorePassword'])
        ->middleware('throttle:api');
   Route::post('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

   Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

//Route::get('test', function() {
//    $car = \App\Models\Car::find(129);
//    $partsList = \DB::table('cars')
//        ->selectRaw('car_pdr_position_cards.id,
//            car_pdrs.item_name_eng as folder,
//            car_pdr_position_cards.parent_inner_id,
//            car_pdr_position_cards.name_eng,
//            car_pdr_position_cards.name_ru,
//            car_pdr_position_cards.ic_number,
//            car_pdr_position_cards.oem_number,
//            car_pdr_position_cards.description as ic_description,
//            car_pdr_position_card_prices.price_currency,
//            car_pdr_position_card_prices.buying_price,
//            car_pdr_position_card_prices.selling_price,
//            car_pdr_positions.user_id,
//            car_pdr_position_cards.barcode,
//            users.name as client_name,
//            car_pdr_position_cards.comment')
//        ->join('car_pdrs', 'car_pdrs.car_id', '=', 'cars.id')
//        ->join('car_pdr_positions','car_pdr_positions.car_pdr_id', '=', 'car_pdrs.id')
//        ->join('car_pdr_position_cards', 'car_pdr_position_cards.car_pdr_position_id', '=', 'car_pdr_positions.id')
//        ->leftJoin('users', 'users.id', '=', 'car_pdr_positions.user_id')
//        ->join('car_pdr_position_card_prices', 'car_pdr_position_card_prices.car_pdr_position_card_id', '=', 'car_pdr_position_cards.id')
//        ->where('cars.id', $car->id)
//        ->whereNull('car_pdr_positions.deleted_at')
//        ->get()->each(function($position) {
//            $card = App\Models\CarPdrPositionCard::with('images', 'createdBy', 'priceCard', 'partAttributesCard', 'comments', 'comments.createdBy')
//                ->find($position->id);
//            $position->images = $card->images ?? [];
//            $position->card = $card ?? null;
//        });
//    return \Pdf::loadView('exports.pdf.dismantling-document', [
//        'parts' => $partsList,
//        'car' => $car,
//    ])->stream();
//});
