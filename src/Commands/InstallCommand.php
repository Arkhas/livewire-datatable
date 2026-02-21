<?php

namespace Arkhas\LivewireDatatable\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'livewire-datatable:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Livewire Datatable: Flux icons and Tailwind @source for package views';

    /**
     * Icons used by the datatable package (Flux / Lucide).
     *
     * @var array<int, string>
     */
    protected array $icons = [
        'circle',
        'timer',
        'check-circle',
        'circle-x',
        'arrow-down',
        'arrow-right',
        'arrow-up',
        'ellipsis-vertical',
        'pencil',
        'trash-2',
        'check',
        'x-mark',
        'arrow-down-tray',
        'adjustments-horizontal',
        'circle-plus',
        'chevron-left',
        'chevron-right',
        'chevrons-left',
        'chevrons-right',
        'chevrons-up-down',
        'magnifying-glass',
    ];

    /**
     * The @source line to add in app.css for package Blade views.
     */
    protected string $sourceLine = "@source '../../vendor/arkhas/livewire-datatable/resources/views/**/*.blade.php';";

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->installIcons();
        $this->addSourceToAppCss();

        $this->newLine();
        $this->info('Livewire Datatable a été installé avec succès.');

        return self::SUCCESS;
    }

    /**
     * Run flux:icon for all package icons.
     */
    protected function installIcons(): void
    {
        $this->info('Installation des icônes Flux…');

        $exitCode = $this->call('flux:icon', [
            'icons' => $this->icons,
        ]);

        if ($exitCode !== self::SUCCESS) {
            $this->warn('La commande flux:icon a échoué. Vérifiez que Livewire Flux est installé.');
        }
    }

    /**
     * Add @source for package views in app.css if not already present.
     */
    protected function addSourceToAppCss(): void
    {
        $paths = [
            base_path('resources/css/app.css'),
            base_path('resources/app.css'),
        ];

        $appCssPath = null;
        foreach ($paths as $path) {
            if (File::isFile($path)) {
                $appCssPath = $path;
                break;
            }
        }

        if (! $appCssPath) {
            $this->warn('Fichier app.css introuvable (ressources/css/app.css ou ressources/app.css). Ajoutez manuellement :');
            $this->line('  ' . $this->sourceLine);
            return;
        }

        $content = File::get($appCssPath);

        if (str_contains($content, 'arkhas/livewire-datatable/resources/views')) {
            $this->comment('La directive @source pour livewire-datatable est déjà présente dans app.css.');
            return;
        }

        $content = rtrim($content) . "\n\n" . $this->sourceLine . "\n";
        File::put($appCssPath, $content);

        $this->info('Directive @source ajoutée dans ' . $appCssPath . '.');
    }
}
