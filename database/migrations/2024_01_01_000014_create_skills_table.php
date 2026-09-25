<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('discipline')->default('General'); // Video, Design, Writing, Development, Voice, Marketing, UGC
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->integer('usage_count')->default(0);
            $table->timestamps();

            $table->index(['discipline', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
