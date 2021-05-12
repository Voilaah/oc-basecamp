<?php namespace Voilaah\Basecamp\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;
use October\Rain\Database\Schema\Blueprint;

class AddGroupColumnToForumChannel extends Migration
{
    public function up()
    {
        Schema::table('rainlab_forum_channels', function (Blueprint $table) {
            $table->integer('permission_group_id')->nullable()->default(null)->after('parent_id');
        });
    }

    public function down()
    {
        Schema::table('rainlab_forum_channels', function (Blueprint $table) {
            if (Schema::hasColumn('rainlab_forum_channels', 'permission_group_id')) {
                $table->dropColumn(['permission_group_id']);
            }
        });
    }
}