<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SduiActionController extends Controller
{
    public function store(Request $request, string $name): JsonResponse
    {
        return match ($name) {
            'profile' => $this->profile(app(UpdateProfileRequest::class)),
            default => throw new NotFoundHttpException("Unknown action [{$name}]."),
        };
    }

    private function profile(UpdateProfileRequest $request): JsonResponse
    {
        $request->user()->update($request->validated());

        return response()->json(['message' => 'Saved.']);
    }
}
