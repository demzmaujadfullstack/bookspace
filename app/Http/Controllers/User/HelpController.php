<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HelpController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = auth()->user();

        $conversations = $user->conversations()
            ->with('latestMessage')
            ->withCount(['messages as unread_count' => fn ($query) => $query->where('sender_id', '!=', $user->id)->whereNull('read_at')])
            ->latest('last_message_at')
            ->paginate(8);

        $settings = Setting::values();

        $openConversation = $user->conversations()
            ->where('status', Conversation::OPEN)
            ->latest('last_message_at')
            ->first();

        $faqs = [
            [
                'q' => 'Bagaimana cara membeli buku di BOOKSPACE?',
                'a' => 'Cari buku yang Anda inginkan melalui menu Katalog atau kolom pencarian. Klik "Tambah ke Keranjang", lalu buka Keranjang Anda dan klik "Lanjut ke Checkout". Isi alamat pengiriman dan pilih metode pembayaran yang diinginkan.',
            ],
            [
                'q' => 'Metode pembayaran apa saja yang didukung?',
                'a' => 'BOOKSPACE mendukung Transfer Bank Manual (BCA, Mandiri, BNI, BRI), QRIS untuk seluruh e-wallet & m-banking, serta Cash on Delivery (COD) bayar di tempat.',
            ],
            [
                'q' => 'Bagaimana cara mengecek status pesanan saya?',
                'a' => 'Masuk ke menu Akun Saya > Pesanan Saya. Anda dapat melihat riwayat semua pesanan lengkap dengan nomor invoice, status verifikasi, dan status pengiriman.',
            ],
            [
                'q' => 'Kapan stok buku akan berkurang setelah dipesan?',
                'a' => 'Stok buku baru akan terpotong secara otomatis setelah pembayaran Anda diverifikasi dan diterima oleh admin BOOKSPACE. Jika Anda membatalkan pesanan atau belum membayar, stok tetap aman.',
            ],
            [
                'q' => 'Bagaimana cara membatalkan pesanan?',
                'a' => 'Pesanan dengan status "Pending", "Menunggu Pembayaran", atau "Menunggu Verifikasi" dapat dibatalkan melalui halaman detail pesanan sebelum admin memproses pengiriman.',
            ],
        ];

        return view('help.index', compact('conversations', 'settings', 'faqs', 'openConversation'));
    }

    public function startConversation(Request $request): RedirectResponse
    {
        $request->validate([
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
        ], [
            'subject.required' => 'Subjek pertanyaan wajib diisi agar admin mudah menindaklanjuti.',
            'message.required' => 'Tuliskan pertanyaan atau kendala Anda terlebih dahulu.',
        ]);

        /** @var User $user */
        $user = auth()->user();

        $conversation = Conversation::create([
            'user_id' => $user->id,
            'subject' => $request->subject,
            'status' => Conversation::OPEN,
            'last_message_at' => now(),
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'body' => $request->message,
        ]);

        return redirect()->route('help.conversations.show', $conversation)->with('success', 'Percakapan berhasil dimulai! Admin akan membalas segera.');
    }

    public function show(Conversation $conversation): View
    {
        /** @var User $user */
        $user = auth()->user();
        abort_unless($conversation->user_id === $user->id, 403);

        // Mark messages from others as read
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $conversation->load(['messages.sender']);

        $conversations = $user->conversations()
            ->withCount(['messages as unread_count' => fn ($query) => $query->where('sender_id', '!=', $user->id)->whereNull('read_at')])
            ->latest('last_message_at')
            ->get();

        return view('help.chat', compact('conversation', 'conversations'));
    }

    public function sendMessage(Request $request, Conversation $conversation): RedirectResponse|JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        abort_unless($conversation->user_id === $user->id, 403);

        $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
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
                    'sender_name' => $user->name,
                    'is_me' => true,
                    'time' => $message->created_at->format('H:i'),
                ],
            ]);
        }

        return back()->with('success', 'Pesan terkirim.');
    }

    public function poll(Conversation $conversation): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        abort_unless($conversation->user_id === $user->id, 403);

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('id')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'body' => $m->body,
                'sender_name' => $m->sender->name,
                'is_me' => $m->sender_id === $user->id,
                'time' => $m->created_at->format('H:i'),
            ]);

        // Mark unread as read
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'status' => $conversation->status,
            'messages' => $messages,
        ]);
    }
}
