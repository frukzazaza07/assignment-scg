<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;

class GoogleCaptchaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $keyGoogleMap = env('GOOGLE_MAP_API_KEY');
        $endPointGoogleRecaptcha = env('GOOGLE_RECAPTCHA_API_ENDPOINT');
        $keyGoogleRecaptcha = env('GOOGLE_RECAPTCHA_API_KEY');
        $payload = [
            "event" => [
                "token" => $request->header('g-recaptcha-response'),
                "siteKey" => $keyGoogleRecaptcha,
            ]
        ];

        $url = "{$endPointGoogleRecaptcha}/assessments?key={$keyGoogleMap}";
        $serviceResponse = Http::post($url, $payload);

        if (!$serviceResponse["tokenProperties"]["valid"]) {
            return response("Please captcha check", 400);
        }
        return $next($request);
    }
}
