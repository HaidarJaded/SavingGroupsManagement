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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saving_group_id')->constrained()->cascadeOnDelete()->nullable();
            $table->foreignId('subscriber_id')->constrained()->cascadeOnDelete()->nullable();
            $table->integer('cycle_number');
            $table->integer('day_number');
            $table->timestamp('payment_date')->useCurrent();
            $table->unique(['saving_group_id','subscriber_id','cycle_number','day_number'], 'unique_payment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
