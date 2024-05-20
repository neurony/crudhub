<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('active')->default(false)->after('remember_token');
            });
        }

        if (!Schema::hasTable('media_unassigned')) {
            Schema::create('media_unassigned', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->boolean('active');
                $table->rememberToken();
                $table->string('two_factor_code', 6)->nullable();
                $table->timestamp('two_factor_code_expires_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('admin_password_resets')) {
            Schema::create('admin_password_resets', function (Blueprint $table) {
                $table->string('email')->index();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_password_resets');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('media_unassigned');

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('active');
            });
        }
    }
};
