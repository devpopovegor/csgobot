<?php

namespace App\Admin\Controllers;


use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function showParserItems()
    {
        $content = '123';
        return AdminSection::view($content, 'Dashboard');
    }
}