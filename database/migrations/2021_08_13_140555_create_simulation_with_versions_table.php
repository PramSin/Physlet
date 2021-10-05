<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSimulationWithVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('simulation_with_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simulation_id');
            $table->string('name');
            $table->string('slug');
            $table->string('root_path');
            $table->text('synopsis');
            $table->unsignedInteger('likes');
            $table->unsignedInteger('shares');
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
        Schema::dropIfExists('simulation_with_versions');
    }
}
