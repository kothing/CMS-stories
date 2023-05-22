<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->dateTime('expired_at')->nullable();
            $table->string('location', 120)->nullable();
            $table->string('key', 120)->unique();
            $table->string('image', 255)->nullable();
            $table->string('url', 255)->nullable();
            $table->bigInteger('clicked')->default(0);
            $table->integer('order')->default(0)->nullable();
            $table->string('status', 60)->default('published');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
