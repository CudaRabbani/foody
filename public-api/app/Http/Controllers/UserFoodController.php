<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserFoodController extends Controller
{
    private $privateApiUrl;

    public function __construct()
    {
        $this->privateApiUrl = env('PRIVATE_API_URL');
    }

    private function checkIfUserIDExists(int $userId) {
        $users = Http::get($this->privateApiUrl."/users/".$userId);
        die($users);
        if ($users) return true;

        return false;
    }

    public function foods(int $userId)
    {

        $user = User::find($userId);
        if (!$user) return response(['error' => 'Invalid user ID'], 404);

        $response = Http::get("{$this->privateApiUrl}/users/{$userId}/foods");
        return $response->json();
    }

    public function food(int $userId, int $foodId)
    {
        $user = User::find($userId);
        if (!$user) return response(['error' => 'Invalid user ID'], 404);
        $food = Food::find($foodId);
        if (!$food) return response(['error' => 'Invalid food ID'], 404);
        $response = Http::get("{$this->privateApiUrl}/users/{$userId}/foods/{$foodId}");
        return $response->json();
    }

    public function deleteFood(int $userId, int $foodId)
    {
        $user = User::find($userId);
        if (!$user) return response(['error' => 'Invalid user ID'], 404);
        $food = Food::find($foodId);
        if (!$food) return response(['error' => 'Invalid food ID'], 404);
        $response = Http::delete("{$this->privateApiUrl}/users/{$userId}/foods/{$foodId}");
        return $response->json();
    }

    public function addFood(Request $request, int $userId, int $foodId)
    {
        $servingsPerWeek = $request->input('servingsPerWeek');

        if (!is_numeric($servingsPerWeek)) return response(['error' => 'Invalid servings per week'], 400);

        $response = Http::put("{$this->privateApiUrl}/users/{$userId}/foods/{$foodId}", [
            'servingsPerWeek' => $servingsPerWeek
        ]);
        return $response->json();
    }
}
