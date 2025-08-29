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
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('business_license_path')->nullable()->after('address');
            $table->string('tax_certificate_path')->nullable()->after('business_license_path');
            $table->string('insurance_certificate_path')->nullable()->after('tax_certificate_path');
            $table->json('additional_documents_paths')->nullable()->after('insurance_certificate_path');
            $table->boolean('documents_verified')->default(false)->after('additional_documents_paths');
            $table->timestamp('documents_verified_at')->nullable()->after('documents_verified');
            $table->text('verification_notes')->nullable()->after('documents_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'business_license_path',
                'tax_certificate_path',
                'insurance_certificate_path',
                'additional_documents_paths',
                'documents_verified',
                'documents_verified_at',
                'verification_notes'
            ]);
        });
    }
};
