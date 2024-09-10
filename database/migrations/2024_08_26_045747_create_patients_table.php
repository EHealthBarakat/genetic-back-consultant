<?php

use App\Enums\DegreeEnum;
use App\Enums\MaritalEnum;
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
        Schema::create('patients', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->foreignUuid( 'user_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignUuid( 'city_id')
                ->nullable()
                ->constrained('cities')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->enum('marital_enum', array_column(MaritalEnum::cases(), 'value'));
            $table->enum('degree_enum', array_column(DegreeEnum::cases(), 'value'));
            $table->char('national_code','10')->fulltext('national_code');
            $table->unique(['national_code', 'deleted_at'], 'patients_national_code_unique');
            $table->char('spouse_national_code','10')->nullable();
            $table->unique(['spouse_national_code', 'deleted_at'], 'patients_spouse_national_code_unique');
            $table->text('address');
            $table->string('father_name');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['national_code', 'spouse_national_code'], 'unique_national_spouse_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
