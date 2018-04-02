<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SeedlingsZO\AskModule\Ask;

class UpdateAskMenu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-ask-menu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '统计更新问答首页菜单';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->comment(PHP_EOL . '--------更新问答首页菜单开始-------' . PHP_EOL);

        $ask = new Ask();
        $ask->getAskMenu(true);

        $this->comment(PHP_EOL . '--------更新问答首页菜单完成-------' . PHP_EOL);
    }
}
