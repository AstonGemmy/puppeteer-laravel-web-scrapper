<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Jobs\Scrapper;
use App\Models\HtmlElement;

class ScrapperController extends Controller {
    
    function addScrapperJobToQueue(Request $request) {
        $request->validate([
            "url" => "required|url",
            "selector" => "required|string"
        ]);

        Scrapper::dispatch($request->only(['url', 'selector']));

        return view('welcome', ['response' => 'Operation successful!']);
    }

    function getElements() {
        $elements = HtmlElement::paginate(5);
        return view('elements', ['elements' => $elements]);
    }

}
