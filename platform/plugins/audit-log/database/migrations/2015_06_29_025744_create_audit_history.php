<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('audit_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned()->index();
            $table->string('module', 60)->index();
            $table->text('request')->nullable();
            $table->string('action', 120);
            $table->text('user_agent')->nullable();
            $table->string('ip_address', 39)->nullable();
            $table->integer('reference_user')->unsigned();
            $table->integer('reference_id')->unsigned();
            $table->string('reference_name', 255);
            $table->string('type', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_histories');
    }
};
