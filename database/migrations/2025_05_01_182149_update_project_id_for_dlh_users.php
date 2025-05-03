<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateProjectIdForDlhUsers extends Migration
{
    public function up()
    {
        // Update project_id untuk user dengan role DLH dan project_id null
        DB::table('users')
            ->where('role_type', 'DLH')
            ->whereNull('project_id')
            ->update(['project_id' => 1]); // Ganti 1 dengan ID project yang sesuai
    }

    public function down()
    {
        // Revert perubahan jika perlu
        DB::table('users')
            ->where('role_type', 'DLH')
            ->where('project_id', 1) // Sesuaikan dengan project_id yang kamu set
            ->update(['project_id' => null]);
    }
}
