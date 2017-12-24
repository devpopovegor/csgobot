<?php

Route::get('', ['as' => 'admin.dashboard', function () {
	$content = 'Define your dashboard here.';
	return AdminSection::view($content, 'Dashboard');
}]);

Route::get('information', ['as' => 'admin.information', function () {
	$content = 'Define your information here.';
	return AdminSection::view($content, 'Information');
}]);

Route::get('information', ['as' => 'pi', function () {

//    $paintseeds = \App\Paintseed::get()->pluck('item_id')->toArray();
    $items = \Illuminate\Support\Facades\DB::select('select distinct items.full_name from items, paintseeds where paintseeds.item_id = items.id');
    $content = view('admin.parser_items', compact('items'));
    return AdminSection::view($content, 'Спарсенные предметы');
}]);
