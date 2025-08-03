<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('xero_organisations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('xero_id')->unique();
            $table->string('name');
            $table->string('country_code');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('xero_organisations');
    }
};
