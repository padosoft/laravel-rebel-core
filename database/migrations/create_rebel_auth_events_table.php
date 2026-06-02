<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rebel_auth_events', function (Blueprint $table): void {
            // ULID: ordinabile per tempo (utile per audit/metriche). Vedi ADR-0005.
            $table->ulid('id')->primary();

            $table->string('tenant_id')->nullable()->index();
            $table->string('event_type')->index();
            $table->string('guard')->nullable();

            $table->string('subject_type')->nullable();
            $table->string('subject_id')->nullable();

            // Identificatore e IP SEMPRE come HMAC (mai in chiaro) + versione pepper.
            // Dimensionati a 128 per supportare algoritmi più larghi di sha256 (es. sha512 = 128 hex).
            $table->string('identifier_hmac', 128)->nullable()->index();
            $table->unsignedTinyInteger('key_version')->nullable();
            $table->string('ip_hmac', 128)->nullable();
            $table->string('user_agent_hash', 128)->nullable();

            $table->string('channel')->nullable();
            $table->string('provider')->nullable();
            $table->string('purpose')->nullable()->index();

            // Assurance raggiunta.
            $table->string('aal', 8)->nullable();
            $table->json('amr')->nullable();

            $table->unsignedTinyInteger('risk_score')->nullable();

            // Hash-chain opzionale per audit immutabile (popolato solo se attivo).
            $table->string('prev_hash', 128)->nullable();

            $table->json('metadata')->nullable();

            $table->timestamp('created_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rebel_auth_events');
    }
};
