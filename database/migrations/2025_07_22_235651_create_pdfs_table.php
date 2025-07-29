<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pdfs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->unsignedInteger('pages')->default(0);
            $table->text('pdf_url'); // URL dari cloud (Cloudinary/Firebase/etc)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdfs');
    }
};
