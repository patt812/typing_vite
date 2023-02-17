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
        Schema::create('setting_plays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setting_id')->constrained();
            $table->tinyInteger('use_type_sound')->default(1);
            $table->tinyInteger('use_beep_sound')->default(1);
            $table->double('volume')->default(0.5);
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
        Schema::dropIfExists('setting_plays');
    }
};
