<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('slugs', function (Blueprint $table) {
            $table->id();
            $table->string('key', 255);
            $table->integer('reference_id')->unsigned();
            $table->string('reference_type', 255);
            $table->string('prefix', 120)->nullable()->default('');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slugs');
    }
};
