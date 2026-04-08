<?php

namespace App\Http\Controllers\Api\V1\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProfileAvatarController extends Controller
{
    public function store(Request $request): Response
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg', 'max:2048'],
        ]);

        $path = $request->file('avatar')->storeAs(
            'avatars',
            $request->user()->id.'.jpg',
            'public'
        );

        $request->user()->update(['avatar_path' => $path]);

        return response()->noContent();
    }
}
