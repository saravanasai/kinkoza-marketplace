<?php

use App\Enums\ListingCategory;
use App\Enums\ListingStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('category')->default(ListingCategory::Machinery->value);
            $table->string('status')->default(ListingStatus::Draft->value);
            $table->unsignedBigInteger('price');
            $table->string('currency', 3);
            $table->string('country', 2);
            $table->string('city');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->index('category');
            $table->index('status');
            $table->index('country');
            $table->index('price');
            $table->index('published_at');
            $table->index('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
