<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentProofController extends Controller
{
    /**
     * Bukti transfer memuat data pribadi (nama, nomor rekening, nominal), jadi
     * file disimpan di disk privat dan hanya bisa dibuka lewat route ini.
     */
    public function show(Payment $payment): StreamedResponse
    {
        $user = auth()->user();

        abort_unless($user->isAdmin() || $payment->order?->user_id === $user->id, 403);
        abort_unless($payment->proof_image && Storage::disk('local')->exists($payment->proof_image), 404);

        return Storage::disk('local')->response($payment->proof_image);
    }
}
