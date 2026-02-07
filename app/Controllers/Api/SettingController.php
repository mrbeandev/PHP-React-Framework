<?php

namespace App\Controllers\Api;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Validation\Validator;
use App\Models\Setting;

class SettingController
{
    public function getSeoToggle(Request $request): Response
    {
        $enabled = Setting::get('enable_dynamic_seo', '1') === '1';
        return Response::json(['enabled' => $enabled]);
    }

    public function updateSeoToggle(Request $request): Response
    {
        $data = Validator::validate($request->json(), [
            'enabled' => 'required|boolean',
        ]);

        $enabled = $data['enabled'];

        Setting::set('enable_dynamic_seo', $enabled ? '1' : '0');

        return Response::json(['success' => true, 'enabled' => $enabled]);
    }
}
