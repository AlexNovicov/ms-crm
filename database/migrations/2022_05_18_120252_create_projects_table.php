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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128);
            $table->string('slug', 128);
            $table->string('crm_subdomain')->unique();
            $table->bigInteger('crm_default_pipeline')->nullable();
            $table->string('crm_client_id', 512)->unique()->nullable();
            $table->string('crm_secret', 512)->nullable();
            $table->string('crm_access_token', 2048)->nullable();
            $table->integer('crm_access_token_expires')->nullable();
            $table->string('crm_refresh_token', 2048)->nullable();
            $table->tinyInteger('status')->default(\App\Models\Project::INACTIVE_STATUS);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
};
