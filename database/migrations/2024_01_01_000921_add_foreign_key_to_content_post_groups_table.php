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
        Schema::table('content_post_groups', function (Blueprint $table) {
            $table->foreign('post_id')->references('id')->on('content_posts')->setNullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('content_post_groups', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
        });
    }
};
