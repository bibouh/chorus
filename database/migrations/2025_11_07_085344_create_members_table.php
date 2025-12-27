<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('member_code', 50)->unique();
            $table->string('qr_code', 100)->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 50);
            $table->text('address')->nullable();
            $table->string('voice_part');
            $table->date('join_date');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('member_code');
            $table->index('qr_code');
            $table->index('email');
            $table->index('voice_part');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
