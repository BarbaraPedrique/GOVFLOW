<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function index(): JsonResponse
    {
        $notes = Note::where('user_id', Auth::id())
            ->orderByDesc('id')
            ->get();

        return response()->json($notes);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'content' => 'required|string|max:5000',
            'color' => 'required|string|in:yellow,green,blue,pink,purple,orange,red,slate',
        ]);

        $note = Note::create([
            'user_id' => Auth::id(),
            'content' => $data['content'],
            'color' => $data['color'],
        ]);

        return response()->json($note, 201);
    }

    public function update(Request $request, Note $note): JsonResponse
    {
        if ($note->user_id !== Auth::id()) abort(403);

        $data = $request->validate([
            'content' => 'required|string|max:5000',
            'color' => 'required|string|in:yellow,green,blue,pink,purple,orange,red,slate',
        ]);

        $note->update($data);

        return response()->json($note);
    }

    public function destroy(Note $note): JsonResponse
    {
        if ($note->user_id !== Auth::id()) abort(403);

        Note::destroy($note->id);

        return response()->json(['success' => true]);
    }
}
