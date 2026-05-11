<?php

use App\Support\UserAccess;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->after('email');
            $table->string('role')->default(UserAccess::ROLE_PA_USER)->after('password');
            // Add the column first; FK is attached only when protected_areas exists.
            $table->foreignId('protected_area_id')->nullable()->after('role');
            $table->softDeletes();
        });

        if (Schema::hasTable('protected_areas')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('protected_area_id')
                    ->references('id')
                    ->on('protected_areas')
                    ->nullOnDelete();
            });
        }

        DB::table('users')
            ->whereNull('username')
            ->orderBy('id')
            ->get(['id', 'email'])
            ->each(function ($user): void {
                $base = strstr((string) $user->email, '@', true) ?: 'user'.$user->id;
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['username' => strtolower($base)]);
            });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('username');
            $table->index('role');
            $table->index('protected_area_id');
        });
    }

    public function down(): void
    {
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['protected_area_id']);
            });
        } catch (\Throwable $e) {
            // Foreign key may not exist if protected_areas was unavailable during up().
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex(['role']);
            $table->dropIndex(['protected_area_id']);
            $table->dropUnique(['username']);
            $table->dropColumn('protected_area_id');
            $table->dropColumn(['role', 'username']);
        });
    }
};
