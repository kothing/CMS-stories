<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('meta_boxes', function (Blueprint $table) {
            $table->id();
            $table->string('meta_key', 255);
            $table->text('meta_value')->nullable();
            $table->integer('reference_id')->unsigned()->index();
            $table->string('reference_type', 120);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meta_boxes');
    }
};
