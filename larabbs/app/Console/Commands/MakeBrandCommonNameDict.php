<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SeedlingsZO\Extend\SimpleDict\SimpleDict;
use SeedlingsZO\PmsModule\Models\PmsBrand;
use SeedlingsZO\PmsModule\Models\PmsProductCommonName;

class MakeBrandCommonNameDict extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:make-brand-common-name-dict';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成品牌通用名标签词典文件';

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
        $this->info(PHP_EOL . '--------正在生成词典-------' . PHP_EOL);

        $brand_dic = storage_path('brand_dic.bin');
        $common_name_dic = storage_path('common_name_dic.bin');

        $brand_model       = new PmsBrand();
        $brand_model       = $brand_model->where('status', 2);
        $common_name_model = new PmsProductCommonName();

        $brand_tags_path       = $this->makeTxt($brand_model, ['id', 'brand_name'], storage_path('brand_tags.txt'));
        $this->info($brand_tags_path . '    done');
        $common_name_tags_path = $this->makeTxt($common_name_model, ['id', 'common_name'], storage_path('common_name_tags.txt'));
        $this->info($common_name_tags_path . '    done');

        $this->makeDict($brand_tags_path, $brand_dic);
        $this->info($brand_dic . '     done');
        $this->makeDict($common_name_tags_path, $common_name_dic);
        $this->info($common_name_dic . '     done');

        $this->info(PHP_EOL . '--------生成词典成功-------' . PHP_EOL);
    }

    /**
     * 生成标签txt
     * @author lili
     * @param $model
     * @param $field
     * @param $out_path
     * @return mixed
     */
    private function makeTxt($model, $field, $out_path)
    {
        $page      = 1;
        $page_size = 2000;

        list($value, $key) = $field;

        $fp = fopen($out_path, 'w') or die("Unable to open file!");

        while (true) {
            $chunk = $model->skip(($page - 1) * $page_size)->take($page_size)->pluck($value, $key);
            $page++;
            if (count($chunk) == 0) {
                break;
            }
            foreach ($chunk as $k => $v) {
                if (trim($k) !== '' && trim($v) !== '') {
                    $line = $k . "\t" . $v . "\n";
                    fwrite($fp, $line);
                }
            }
        }
        fclose($fp);

        return $out_path;

    }

    /**
     * 生成词典文件
     * @param $input_path
     * @param $out_path
     */
    private function makeDict($input_path, $out_path)
    {
        SimpleDict::make($input_path, $out_path);
    }
}
