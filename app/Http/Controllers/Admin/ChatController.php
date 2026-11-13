<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(Request $request): View
    {
        $query = Conversation::with(['user', 'latestMessage'])
            ->latest('last_message_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $conversations = $query->paginate(15)->withQueryString();

        $openCount = Conversation::where('status', Conversation::OPEN)->count();
        $closedCount = Conversation::where('status', Conversation::CLOSED)->count();

        return view('admin.chat.index', compact('conversations', 'openCount', 'closedCount'));
    }

    public function show(Conversation $conversation): View
    {
        $conversation->load(['user.orders', 'messages.sender']);

        // Mark messages from the user as read
        $conversation->messages()
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('admin.chat.show', compact('conversation'));
    }

    public function sendMessage(Request $request, Conversation $conversation): RedirectResponse|JsonResponse
    {
        $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'body' => $request->body,
        ]);

        $conversation->update([
            'last_message_at' => now(),
            'status' => Conversation::OPEN,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'body' => $message->body,
                    'sender_name' => auth()->user()->name,
                    'is_me' => true,
                    'time' => $message->created_at->format('H:i'),
                ],
            ]);
        }

        return back()->with('success', 'Balasan terkirim.');
    }

    public function close(Conversation $conversation): RedirectResponse
    {
        $newStatus = $conversation->status === Conversation::OPEN ? Conversation::CLOSED : Conversation::OPEN;
        $conversation->update(['status' => $newStatus]);

        $label = $newStatus === Conversation::CLOSED ? 'ditandai selesai' : 'dibuka kembali';

        return back()->with('success', "Percakapan berhasil {$label}.");
    }

    public function poll(Conversation $conversation): JsonResponse
    {
        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('id')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'body' => $m->body,
                'sender_name' => $m->sender->name,
                'is_me' => $m->sender_id === auth()->id(),
                'time' => $m->created_at->format('H:i'),
            ]);

        $conversation->messages()
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'status' => $conversation->status,
            'messages' => $messages,
        ]);
    }
}
