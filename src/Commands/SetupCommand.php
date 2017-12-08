<?php

namespace InetStudio\Ingredients\Commands;

use Illuminate\Console\Command;

class SetupCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'inetstudio:ingredients:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup ingredients package';

    /**
     * Commands to call with their description.
     *
     * @var array
     */
    protected $calls = [];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->initCommands();

        foreach ($this->calls as $info) {
            if (! isset($info['command'])) {
                continue;
            }

            $this->line(PHP_EOL.$info['description']);
            $this->call($info['command'], $info['params']);
        }
    }

    /**
     * Инициализация команд.
     *
     * @return void
     */
    private function initCommands()
    {
        $this->calls = [
            (! class_exists('CreateLikeCounterTable')) ? [
                'description' => 'Likeable setup',
                'command' => 'vendor:publish',
                'params' => [
                    '--provider' => 'Cog\Likeable\Providers\LikeableServiceProvider',
                    '--tag' => 'migrations',
                ],
            ] : [],
            [
                'description' => 'Publish migrations',
                'command' => 'vendor:publish',
                'params' => [
                    '--provider' => 'InetStudio\Ingredients\IngredientsServiceProvider',
                    '--tag' => 'migrations',
                ],
            ],
            [
                'description' => 'Migration',
                'command' => 'migrate',
                'params' => [],
            ],
            [
                'description' => 'Create folders',
                'command' => 'inetstudio:ingredients:folders',
                'params' => [],
            ],
            [
                'description' => 'Publish public',
                'command' => 'vendor:publish',
                'params' => [
                    '--provider' => 'InetStudio\Ingredients\IngredientsServiceProvider',
                    '--tag' => 'public',
                    '--force' => true,
                ],
            ],
            [
                'description' => 'Publish config',
                'command' => 'vendor:publish',
                'params' => [
                    '--provider' => 'InetStudio\Ingredients\IngredientsServiceProvider',
                    '--tag' => 'config',
                ],
            ],
        ];
    }
}
