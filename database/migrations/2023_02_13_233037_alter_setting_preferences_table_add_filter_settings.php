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
        Schema::table('setting_preferences', function (Blueprint $table) {
            $table->smallInteger('max_accuracy')->unsigned()->nullable()->after('is_random');
            $table->smallInteger('min_accuracy')->unsigned()->default(0)->nullable()->after('is_random');
            $table->tinyInteger('limit_accuracy')->default(0)->after('is_random');
            $table->smallInteger('max_wpm')->unsigned()->nullable()->after('is_random');
            $table->smallInteger('min_wpm')->unsigned()->default(0)->nullable()->after('is_random');
            $table->tinyInteger('limit_wpm')->default(0)->after('is_random');
            $table->tinyInteger('prior_no_stats')->default(1)->after('is_random');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('setting_preferences', function (Blueprint $table) {
            $table->dropColumn('limit_wpm');
            $table->dropColumn('min_wpm');
            $table->dropColumn('max_wpm');
            $table->dropColumn('limit_accuracy');
            $table->dropColumn('min_accuracy');
            $table->dropColumn('max_accuracy');
            $table->dropColumn('prior_no_stats');
        });
    }
};
