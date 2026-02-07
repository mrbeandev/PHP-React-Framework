<?php

use App\Controllers\Web\FrontendController;
use App\Core\Exceptions\HttpException;
use App\Core\Http\Request;

$router->fallback(function (Request $request, FrontendController $frontendController) {
    if ($request->isApiRequest()) {
        throw new HttpException(404, 'API Endpoint Not Found', ['error' => 'API Endpoint Not Found']);
    }

    return $frontendController->serve($request);
});
