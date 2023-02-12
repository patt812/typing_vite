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
        Schema::table('sentence_stats', function (Blueprint $table) {
            $table->dropForeign('sentence_stats_sentence_id_foreign');
            $table->foreign('sentence_id')
            ->references('id')->on('sentences')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sentence_stats', function (Blueprint $table) {
            $table->dropForeign('sentence_stats_sentence_id_foreign');
            $table->dropColumn('sentence_id');
        });
        Schema::table('sentence_stats', function (Blueprint $table) {
            $table->foreignId('sentence_id')->constrained();
        });
    }
};
