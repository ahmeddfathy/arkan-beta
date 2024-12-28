<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('absence_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('absence_date');
            $table->string('reason');

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->enum('manager_approval', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('manager_rejection_reason')->nullable();

            $table->enum('leader_approval', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('leader_rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('absence_requests');
    }
};
