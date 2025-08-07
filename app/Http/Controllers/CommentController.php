<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function storeForIncident(Request $request, Incident $incident)
    {
        $request->validate([
            'body' => 'required|string',
        ]);

        $incident->comments()->create([
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}
