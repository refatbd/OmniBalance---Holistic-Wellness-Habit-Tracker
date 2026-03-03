<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController extends Controller
{
    public function update(Request $request)
    {
        // CHANGED: Use $request->validate() instead of $this->validate()
        $request->validate(['endpoint' => 'required']);
        
        $request->user()->updatePushSubscription(
            $request->endpoint,
            $request->keys['p256dh'] ?? null,
            $request->keys['auth'] ?? null
        );

        return response()->json(['success' => true]);
    }
}