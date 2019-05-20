<?php

namespace InetStudio\IngredientsPackage\Console\Commands;

use InetStudio\AdminPanel\Base\Console\Commands\BaseSetupCommand;

/**
 * Class SetupCommand.
 */
class SetupCommand extends BaseSetupCommand
{
    /**
     * Имя команды.
     *
     * @var string
     */
    protected $name = 'inetstudio:ingredients-package:setup';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Setup ingredients package';

    /**
     * Инициализация команд.
     */
    protected function initCommands(): void
    {
        $this->calls = [
            [
                'type' => 'artisan',
                'description' => 'Statuses setup',
                'command' => 'inetstudio:ingredients-package:ingredients:setup',
            ],
        ];
    }
}
