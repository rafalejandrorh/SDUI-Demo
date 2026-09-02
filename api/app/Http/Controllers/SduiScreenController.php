<?php

namespace App\Http\Controllers;

use App\Sdui\ScreenCatalog;
use App\Sdui\ScreenEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SduiScreenController extends Controller
{
    public function __construct(private ScreenCatalog $screens) {}

    public function show(Request $request, string $name): JsonResponse
    {
        if (! $this->screens->has($name)) {
            throw new NotFoundHttpException("Unknown screen [{$name}].");
        }

        $locale = $request->getPreferredLanguage(['en', 'es']) ?: app()->getLocale();
        $key = "sdui:screen:{$name}:{$locale}";

        $tree = Cache::remember($key, now()->addHour(), fn () => $this->screens->build($name));

        return response()->json(ScreenEnvelope::wrap($name, $tree));
    }
}
