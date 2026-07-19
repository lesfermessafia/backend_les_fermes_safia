<?php

namespace App\Http\Controllers;

use App\Events\DiscussionMessageSent;
use App\Models\DiscussionMessage;
use App\Services\StockNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DiscussionController extends Controller
{
    public function index(): View
    {
        $messages = DiscussionMessage::with('sender')
            ->latest()
            ->limit(100)
            ->get()
            ->reverse()
            ->values();

        DiscussionMessage::whereNull('read_at')
            ->where('sender_id', '!=', Auth::id())
            ->update(['read_at' => now()]);

        return view('discussion.index', compact('messages'));
    }

    public function latest(Request $request): JsonResponse
    {
        $after = (int) $request->integer('after', 0);

        $messages = DiscussionMessage::with('sender')
            ->where('id', '>', $after)
            ->oldest()
            ->limit(50)
            ->get()
            ->map(fn (DiscussionMessage $message) => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_id' => $message->sender_id,
                'sender_name' => trim($message->sender->prenom . ' ' . $message->sender->nom),
                'created_at' => $message->created_at->format('H:i'),
            ]);

        return response()->json(['messages' => $messages]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $message = DiscussionMessage::create([
            'sender_id' => Auth::id(),
            'message' => trim($validated['message']),
        ])->load('sender');

        StockNotificationService::notifyRolesExcept(
            Auth::id(),
            'Nouveau message de discussion',
            $message->sender->prenom . ' ' . $message->sender->nom . ' vous a envoyé un message.',
            'discussion',
            route('discussion.index'),
            'blue'
        );

        broadcast(new DiscussionMessageSent($message))->toOthers();

        return response()->json([
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_id' => $message->sender_id,
                'sender_name' => trim($message->sender->prenom . ' ' . $message->sender->nom),
                'created_at' => $message->created_at->format('H:i'),
            ],
        ], 201);
    }
}
