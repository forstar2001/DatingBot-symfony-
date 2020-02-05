<?php


namespace App\Http\Controllers;


use App\Models\SourceBotProfileStatuses;
use Illuminate\Http\Request;

class SourceBotProfileStatusesController extends Controller
{
    public function get(Request $request)
    {
        $result['success'] = true;

        if ($result['success'] === true) {
            try {
                $data = SourceBotProfileStatuses::get();
                $result['data'] = $data;
                return json_encode($result);
            } catch (\Exception $exception) {
                $result['success'] = false;
                $result['errors'] = array($exception->getMessage());
                return json_encode($result);
            }
        } else {
            return json_encode($result);
        }
    }
}