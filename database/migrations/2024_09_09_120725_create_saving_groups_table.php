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
        Schema::create('saving_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('subscribers_count');
            $table->double('amount_per_day');
            $table->integer('days_per_cycle');
            $table->integer('current_cycle')->default(1);
            $table->integer('current_day')->default(1);
            $table->double('total_amount');
            $table->date('start_date')->useCurrent();
            $table->date('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saving_groups');
    }
};
