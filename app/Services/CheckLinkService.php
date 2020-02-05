<?php


namespace App\Services;


use App\Models\SourcesBotProfiles;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class CheckLinkService
{
    public function check(SourcesBotProfiles $sourcesBotProfiles)
    {
        try{
            $linkCheckerUrl = 'http://139.180.145.16';
            $options = [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            ];
            $data = [
                'id' => $sourcesBotProfiles->id,
                'link' => $sourcesBotProfiles->link,
            ];

            $client = new Client($options);

            $client->post($linkCheckerUrl . '/api/check-link', [
                'form_params' => $data,
                'timeout' => '15'
            ]);

        }catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}