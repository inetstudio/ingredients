<?php

namespace InetStudio\Ingredients\Commands;

use Illuminate\Console\Command;

class CreateFoldersCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'inetstudio:ingredients:folders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create package folders';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        // TODO использовать конфиг

        $path = storage_path().'/app/public/ingredients';

        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }
}
