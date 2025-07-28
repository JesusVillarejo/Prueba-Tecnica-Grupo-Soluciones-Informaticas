<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RestartProject extends Command
{
    protected $signature   = 'restart:project';
    protected $description = '✅ Limpia, migra y copia AdminLTE + FullCalendar UMD bundle.';

    public function handle()
    {
        $this->info('✅ Reiniciando…');

        // 1) Limpiar cachés
        foreach (['config:clear','route:clear','view:clear','cache:clear'] as $cmd) {
            $this->callSilent($cmd);
        }
        $this->info('Cachés limpiadas.');

        // 2) Migración fresca + seed
        $this->call('migrate:fresh', ['--seed' => true]);
        $this->info('Migración + seed OK.');

        // 3) Copiar AdminLTE
        $this->call('adminlte:copiar-assets');
        $this->info('Assets de AdminLTE copiados.');

        // 4) Copiar FullCalendar UMD bundle (core + plugins)
        $dstDir = public_path('vendor/adminlte/plugins/fullcalendar');
        File::ensureDirectoryExists($dstDir);

        foreach (['core', 'interaction', 'daygrid', 'timegrid'] as $pkg) {
            $src = base_path("node_modules/@fullcalendar/{$pkg}/index.global.min.js");
            $dst = "{$dstDir}/{$pkg}.global.min.js";

            if (File::exists($src)) {
                File::copy($src, $dst);
                $this->info("✅ FullCalendar {$pkg}.global.min.js copiado.");
            } else {
                $this->warn("⚠️ Falta {$src}. Instala: npm install @fullcalendar/{$pkg}@6.1.18");
            }

            // Solo core trae CSS
            if ($pkg === 'core') {
                $srcCss = base_path("node_modules/@fullcalendar/core/index.global.min.css");
                $dstCss = "{$dstDir}/core.global.min.css";
                if (File::exists($srcCss)) {
                    File::copy($srcCss, $dstCss);
                    $this->info("✅ core.global.min.css copiado.");
                }
            }
        }

        // 5) (Opcional) volver a publicar config de DomPDF si cambias algo
        $this->callSilent('vendor:publish', [
            '--provider' => 'Barryvdh\DomPDF\ServiceProvider',
            '--tag'      => 'config',
            '--force'    => true,
        ]);
        $this->info('Configuración de DomPDF publicada.');

        // 6) Publicar vistas + config de AdminLTE
        foreach ([
            ['--provider'=>'JeroenNoten\LaravelAdminLte\AdminLteServiceProvider','--tag'=>'views','--force'=>true],
            ['--provider'=>'JeroenNoten\LaravelAdminLte\AdminLteServiceProvider','--tag'=>'config','--force'=>true],
        ] as $params) {
            $this->callSilent('vendor:publish', $params);
        }
        $this->info('Vistas y config de AdminLTE publicadas.');

        // 7) Limpiar vistas compiladas
        $this->callSilent('view:clear');
        $this->info('✅ Proyecto listo desde cero.');

        return Command::SUCCESS;
    }
}
