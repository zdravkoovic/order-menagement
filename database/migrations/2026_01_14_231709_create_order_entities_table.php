<?php

use App\Domain\OrderAggregate\OrderState;
use App\Domain\OrderAggregate\PaymentMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_entities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference', 20)->unique()->nullable();
            $table->enum('payment_method', PaymentMethod::cases())->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->uuid('customer_id')->require();
            $table->enum('state', OrderState::cases())->default(OrderState::DRAFT);
            $table->timestamp('expires_at');
            $table->timestamps();
            $table->uuid('draft_customer_id')
                ->nullable()
                ->storedAs("CASE WHEN state = 'DRAFT' THEN customer_id ELSE NULL END");
            $table->unique('draft_customer_id');
        });

        DB::statement("
            CREATE UNIQUE INDEX uniq_customer_draft_order
            ON order_entities (customer_id)
            WHERE state = 'DRAFT'
        ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_entities');
    }
};
