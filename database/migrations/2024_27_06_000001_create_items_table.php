<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('section_id')->index();
            $table->string('month', 7);
            $table->string('name', 100);
            $table->integer('amount')->nullable();
            $table->string('icon', 50)->nullable();
            $table->integer('quota')->nullable();
            $table->integer('quota_total')->nullable();
            $table->boolean('payed')->nullable();
            $table->date('payed_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['section_id', 'month', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
