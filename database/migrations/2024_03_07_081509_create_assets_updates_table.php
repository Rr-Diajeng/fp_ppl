<?php

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
        Schema::create('assets_updates', function (Blueprint $table) {
            $table->id('UpdateID');
            $table->string('namaAsset', 100);
            $table->text('description');
            $table->decimal('depreciation', 10, 2);
            $table->string('assetImage');
            $table->integer('price');
            $table->date('purchaseDate');
            $table->string('QRCode');
            $table->string('slug');
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->text('reason');
            $table->unsignedBigInteger('assetID');
            $table->unsignedBigInteger('UpdatedBy');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('assetID')->references('id')->on('assets');
            $table->foreign('UpdatedBy')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets_updates');
    }
};
