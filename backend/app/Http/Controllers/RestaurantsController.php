<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use App\Models\GoogleRequestLogService;
use Exception;
use Illuminate\Support\Facades\Cache;

class RestaurantsController extends Controller
{
    public function __construct()
    {
        $this->endPointGoogleMap = env('GOOGLE_MAP_API_ENDPOINT');
        $this->keyGoogleMap = env('GOOGLE_MAP_API_KEY');
        $this->endPointGoogleRecaptcha = env('GOOGLE_RECAPTCHA_API_ENDPOINT');
        $this->keyGoogleRecaptcha = env('GOOGLE_RECAPTCHA_API_KEY');
    }

    public $endPointGoogleMap = "";
    public $keyGoogleMap = "";
    public $endPointGoogleRecaptcha = "";
    public $keyGoogleRecaptcha = "";
    /**
     * Display a listing of the resource.
     */
    public function initMap(Request $request)
    {
        $url = "{$this->endPointGoogleMap}/api/js?key={$this->keyGoogleMap}";
        try {
            $response = Http::get($url);
            $this->saveRequestLog($request, $url, 'get', null);
            return  $response;
        } catch (Exception $e) {
            $this->saveRequestLog($request, $url, 'get', $e->getMessage());
            return response('Service Not Available', 503);
        }
    }

    public function getDefaultLocation(Request $request)
    {
        return (object) ["lat" => 13.803286999767622, "lng" => 100.53851260000002, "title" => 'Bang sue'];
    }

    public function findPlaces(Request $request)
    {
        if (Cache::has($request->searchPlace)) {
            return Cache::get($request->searchPlace);
        }

        // get place
        $data =  $this->findPlaceFromText($request);
        if (count($data["candidates"]) === 0) {
            return response("Not found {$request->searchPlace}", 404);
        }
        $locationLatLng = $data["candidates"][0]["geometry"]["location"];
        // set query parameter
        $options = http_build_query([
            "keyword" => $request->searchPlace,
            "location" => implode(',', $locationLatLng),
            "radius" => '100000',
            "type" => 'restaurant',
        ]);

        $url = "{$this->endPointGoogleMap}/api/place/nearbysearch/json?{$options}&key={$this->keyGoogleMap}";
        try {
            $response = json_decode(Http::post($url));
            // save log
            $this->saveRequestLog($request, $url, 'post', json_encode($response), $options);
            // call google api
            Cache::put($request->searchPlace, $response, now()->addMinutes(60));
            return $response;
        } catch (Exception $e) {
            $this->saveRequestLog($request, $url, 'post', $e->getMessage(), $options);
            return response('Service Not Available', 503);
        }
    }

    public function findPlaceFromText(Request $request)
    {
        // set query parameter
        $options = http_build_query([
            "fields" => 'formatted_address,name,rating,opening_hours,geometry',
            "input" => isset($request->searchPlace) || '',
            "inputtype" => 'textquery',
        ]);
        // url call google map service
        $url = "{$this->endPointGoogleMap}/api/place/findplacefromtext/json?{$options}&key={$this->keyGoogleMap}";
        try {
            $response = Http::post($url);
            // save log
            $this->saveRequestLog($request, $url, 'post', json_encode($response), $options);
            return $response;
        } catch (Exception $e) {
            $this->saveRequestLog($request, $url, 'post', $e->getMessage(), $options);
            return response('Service Not Available', 503);
        }
    }

    public function placePhoto(Request $request)
    {
        // set query parameter
        $options = http_build_query([
            "maxwidth" => "600",
            "photo_reference" => $request->photoRef,
        ]);
        $url = "{$this->endPointGoogleMap}/api/place/photo?{$options}&key={$this->keyGoogleMap}";
        try {
            // save log
            $this->saveRequestLog($request, $url, 'get', 'file');
            // call google api
            $response = Http::withHeaders(["responseType" => 'stream'])->get($url);
            return  $response;
        } catch (Exception $e) {
            $this->saveRequestLog($request, $url, 'get', $e->getMessage());
            return response('Service Not Available', 503);
        }
    }

    private function saveRequestLog(Request $request, $googleEndPoint, $method, $googleResponse, $googlePayload = null)
    {
        // // payload insert request google service log
        $columInsertLog = [
            "gml_user_ip" => $request->ip,
            "gml_request_url" => $request->url,
            "gml_request_payload" => $googlePayload || $googlePayload,
            "gml_google_key" => $this->keyGoogleMap,
            "gml_google_url" => $googleEndPoint,
            "gml_google_request" => $googleEndPoint,
            "gml_google_request_method" => $method,
            "gml_google_response" => $googleResponse,
        ];

        return GoogleRequestLogService::create($columInsertLog);
    }
}
