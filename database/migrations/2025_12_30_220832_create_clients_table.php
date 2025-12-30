<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);
            $table->string('codename', 100)->nullable();
            $table->string('document', 14)->unique()->nullable();
            $table->string('email', 100)->unique()->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('address', 100)->nullable();
            $table->string('number', 10)->nullable();
            $table->string('complement')->nullable();
            $table->string('district', 50)->nullable();
            $table->string('state', 2)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('zip_code', 8)->nullable();

            $table->string('stateRegistration', 20)->nullable();
            $table->string('origin', 20)->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
