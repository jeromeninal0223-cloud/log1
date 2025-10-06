<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('contracts', 'workflow_status')) {
            Schema::table('contracts', function (Blueprint $table) {
                // Contract workflow fields
                $table->string('workflow_status')->default('draft')->after('status');
                $table->decimal('negotiated_value', 15, 2)->nullable()->after('value');
                $table->json('negotiated_terms')->nullable()->after('terms');
                $table->text('negotiation_notes')->nullable();
                
                // Digital signature fields
                $table->timestamp('vendor_signed_at')->nullable();
                $table->string('vendor_signature_hash')->nullable();
                $table->string('vendor_signature_ip')->nullable();
                
                $table->timestamp('procurement_signed_at')->nullable();
                $table->string('procurement_signature_hash')->nullable();
                $table->string('procurement_signature_ip')->nullable();
                $table->unsignedBigInteger('procurement_officer_id')->nullable();
                
                // Contract document fields
                $table->string('draft_document_path')->nullable();
                $table->string('final_document_path')->nullable();
                $table->json('revision_history')->nullable();
                
                // Approval workflow
                $table->timestamp('sent_for_review_at')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                
                $table->foreign('procurement_officer_id')->references('id')->on('users');
                $table->foreign('approved_by')->references('id')->on('users');
            });
        }
    }

    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign(['procurement_officer_id']);
            $table->dropForeign(['approved_by']);
            
            $table->dropColumn([
                'workflow_status',
                'negotiated_value',
                'negotiated_terms',
                'negotiation_notes',
                'vendor_signed_at',
                'vendor_signature_hash',
                'vendor_signature_ip',
                'procurement_signed_at',
                'procurement_signature_hash',
                'procurement_signature_ip',
                'procurement_officer_id',
                'draft_document_path',
                'final_document_path',
                'revision_history',
                'sent_for_review_at',
                'approved_at',
                'approved_by'
            ]);
        });
    }
};
