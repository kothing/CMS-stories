<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->longText('description');
            $table->tinyInteger('is_featured')->unsigned()->default(0);
            $table->tinyInteger('order')->unsigned()->default(0);
            $table->string('image', 255)->nullable();
            $table->integer('user_id')->unsigned()->index();
            $table->string('status', 60)->default('published');
            $table->timestamps();
        });

        Schema::create('gallery_meta', function (Blueprint $table) {
            $table->id();
            $table->text('images')->nullable();
            $table->integer('reference_id')->unsigned()->index();
            $table->string('reference_type', 120);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galleries');
        Schema::dropIfExists('gallery_meta');
    }
};
