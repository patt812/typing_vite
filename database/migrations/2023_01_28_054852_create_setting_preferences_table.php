<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setting_id')->constrained();
            $table->smallInteger('sentences')->default(5);
            $table->tinyInteger('is_random')->default(1);
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
        Schema::dropIfExists('setting_preferences');
    }
};
