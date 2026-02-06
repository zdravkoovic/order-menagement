<?php

use App\Domain\OrderAggregate\ValueObjects\OrderState;
use App\Domain\OrderAggregate\ValueObjects\PaymentMethod;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference', 20)->unique()->nullable();
            $table->enum('payment_method', PaymentMethod::cases())->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->uuid('customer_id')->require();
            $table->enum('state', OrderState::cases())->default(OrderState::DRAFT);
            $table->timestamp('expires_at');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_entities');
    }
};
