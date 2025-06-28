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
        Schema::table('kycs', function (Blueprint $table) {
            $table->timestamp('validated_at')->nullable()->after('status');
            $table->foreignId('validated_by')->nullable()->after('validated_at')->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable()->after('validated_by');
            $table->foreignId('rejected_by')->nullable()->after('rejected_at')->constrained('users')->onDelete('set null');
            $table->text('raison_rejet')->nullable()->after('rejected_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kycs', function (Blueprint $table) {
            $table->dropForeign(['validated_by']);
            $table->dropForeign(['rejected_by']);
            $table->dropColumn([
                'validated_at',
                'validated_by',
                'rejected_at',
                'rejected_by',
                'raison_rejet'
            ]);
        });
    }
};
