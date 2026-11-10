<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\HomepageBuilderService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        protected HomepageBuilderService $homepageBuilderService
    ) {}

    public function index(Request $request)
    {
        $forceRefresh = $request->boolean('refresh', false);
        $data = $this->homepageBuilderService->resolveHomepageData($forceRefresh);

        return view('frontend.home', $data);
    }
}
