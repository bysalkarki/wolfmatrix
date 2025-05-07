<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("tickets", function (Blueprint $table) {
            $table->id();
            $table->string("event_name");
            $table->string("phone");
            $table
                ->enum("status", ["pending", "confirmed", "cancelled"])
                ->default("pending");
            $table
                ->foreignIdFor(User::class)
                ->nullable()
                ->constrained()
                ->onDelete("cascade");
            $table->string("ticket_code")->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("tickets");
    }
};
