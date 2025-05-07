<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function reserve(Request $request, int $ticketId)
    {
        DB::transaction(function () use($ticketId) {
            $ticket = Ticket::query()
                ->where("id", '=', $ticketId)
                ->lockForUpdate()
                ->firstorfail();

            $ticket->update([
                "status" => "confirmed",
                "user_id" => auth()->id(),
                "reserved_at" => now(),
            ]);
        });

        return response()->json([
            "message" => "Ticket reserved successfully",
        ]);
    }
}
