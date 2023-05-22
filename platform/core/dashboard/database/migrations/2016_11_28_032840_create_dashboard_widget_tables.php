<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('dashboard_widgets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->timestamps();
        });

        Schema::create('dashboard_widget_settings', function (Blueprint $table) {
            $table->id();
            $table->text('settings')->nullable();
            $table->integer('user_id')->unsigned()->index();
            $table->integer('widget_id')->unsigned()->index();
            $table->tinyInteger('order')->unsigned()->default(0);
            $table->tinyInteger('status')->unsigned()->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_widgets');
        Schema::dropIfExists('dashboard_widget_settings');
    }
};
