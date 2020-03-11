<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelegramUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegram_users', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->index();

            $table->string('username', 255)->nullable();

            $table->string('first_name', 128)->nullable();
            $table->string('last_name', 128)->nullable();

            $table->string('language_code', 16)->nullable();

            $table->boolean('is_bot')->default(false);

            $table->string('context')->nullable();
            $table->text('payload')->nullable();

            $table->string('image')->nullable();
            $table->string('phone')->nullable();
            $table->string('name')->nullable();
            $table->string('category')->nullable();
            $table->text('feedback')->nullable();

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
        Schema::dropIfExists('telegram_users');
    }
}
