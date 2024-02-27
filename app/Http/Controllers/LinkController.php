<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index($id)
    {
        return Link::where('user_id', $id)->get();
    }
}