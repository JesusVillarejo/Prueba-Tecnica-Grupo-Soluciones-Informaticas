<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AdminLteCopiarAssets extends Command
{
    protected $signature = 'adminlte:copiar-assets';
    protected $description = '✅ Copia CSS/JS y vistas Blade de AdminLTE a public/ y resources/.';

    public function handle()
    {
        // --- 1) Copiar assets front (JS/CSS/plugins) ---
        $origenAssets = base_path('vendor/almasaeed2010/adminlte');
        $destAssets  = public_path('vendor/adminlte');
        File::ensureDirectoryExists($destAssets);
        File::copyDirectory($origenAssets . '/dist',    $destAssets . '/dist');
        File::copyDirectory($origenAssets . '/plugins', $destAssets . '/plugins');
        $this->info("✅ Assets de AdminLTE copiados en public/vendor/adminlte");

        // --- 2) Copiar vistas Blade del paquete ---
        $origenViews = base_path('vendor/jeroennoten/laravel-adminlte/resources/views');
        $destViews   = resource_path('views/vendor/adminlte');
        if (File::exists($origenViews)) {
            File::ensureDirectoryExists($destViews);
            File::copyDirectory($origenViews, $destViews);
            $this->info("✅ Vistas de AdminLTE copiadas en resources/views/vendor/adminlte");
        } else {
            $this->warn("✅ No encontré el directorio de views en jeroennoten/laravel-adminlte");
        }

        return Command::SUCCESS;
    }
}
