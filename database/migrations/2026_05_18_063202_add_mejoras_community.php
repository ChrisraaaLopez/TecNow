<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Solo insertar si no existe ya (seguro en producción)
        if (DB::table('communities')->where('slug', 'mejoras-y-sugerencias')->doesntExist()) {
            $adminId = DB::table('users')->where('rol', 'admin')->value('id')
                    ?? DB::table('users')->value('id');

            DB::table('communities')->insert([
                'name'        => 'Mejoras y Sugerencias',
                'icon'        => '💡',
                'description' => 'Espacio para proponer mejoras, reportar bugs y sugerir nuevas funcionalidades para TecNow.',
                'tipo'        => 'general',
                'slug'        => 'mejoras-y-sugerencias',
                'created_by'  => $adminId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('communities')->where('slug', 'mejoras-y-sugerencias')->delete();
    }
};
