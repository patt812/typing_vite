<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sentence_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sentence_id')->constrained();
            $table->bigInteger('played')->default(0);
            $table->double('min_wpm');
            $table->double('max_wpm');
            $table->double('ave_wpm');
            $table->double('min_accuracy');
            $table->double('max_accuracy');
            $table->double('ave_accuracy');
            $table->bigInteger('perfect')->default(0);
            $table->smallInteger('max_miss_streak')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sentence_stats');
    }
};
