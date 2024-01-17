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
        Schema::create('crm_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('name', 128);
            $table->string('title', 128);
            $table->integer('crm_id');
            $table->tinyInteger('crm_entity');
            $table->string('crm_type', 16);
            $table->string('crm_enum', 16)->nullable();
            $table->tinyInteger('entity');
            $table->string('entity_field', 32)->comment('Поле сущности.');
            $table->tinyInteger('type')->default(1);
            $table->string('type_format', 16)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade')->onUpdate('cascade');

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crm_fields');
    }
};
