<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtpAndRoleToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('otp_code')->nullable()->after('role');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([ 'otp_code', 'otp_expires_at']);
        });
    }
}
