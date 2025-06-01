<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ViewGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'view:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate all resource views wrapped in <x-app-layout> with a labeled div';

    /**
     * Filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Resource names and the templates to generate.
     *
     * @var array
     */
    protected $resources = [
        'pengguna',
        'kategori-pemasukan',
        'kategori-pengeluaran',
        'pemasukan',
        'pengeluaran',
        'anggaran',
        'rekening',
        'transfer',
        'utang',
        'pembayaran-utang',
        'piutang',
        'pembayaran-piutang',
        'laporan',
        'admin',
    ];

    protected $templates = [
        'index',
        'create',
        'edit',
        'show',
    ];

    /**
     * Create a new command instance.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $base = resource_path('views');
        foreach ($this->resources as $resource) {
            $dir = "$base/{$resource}";
            if (! $this->files->isDirectory($dir)) {
                $this->files->makeDirectory($dir, 0755, true);
                $this->info("Created directory: views/{$resource}");
            }

            foreach ($this->templates as $tpl) {
                $file = "$dir/{$tpl}.blade.php";
                $label = ucfirst(str_replace('-', ' ', $resource)) . ' ' . ucfirst($tpl);
                $content = <<<BLADE
<x-app-layout>
<div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
  <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
    {{ "$label" }}
  </h1>

  <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
    {{-- TODO: add your markup here --}}
    <p class="text-sm text-gray-600 dark:text-gray-300">Drop your content here 🔥</p>
  </div>
</div>

</x-app-layout>
BLADE;

                $this->files->put($file, $content);
                $this->info("Generated view: views/{$resource}/{$tpl}.blade.php");
            }
        }

        $this->info('All resource views generated successfully.');
    }
}
