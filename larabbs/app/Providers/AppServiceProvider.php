<?php

namespace App\Providers;

use Carbon\Carbon;
use DB;
use Illuminate\Support\ServiceProvider;
use SeedlingsZO\Validator\ExtendValidator;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (env('APP_ENV') == 'local') {
            //监听记录数据库执行日志
            DB::listen(function ($query) {
                $content = '[' . Carbon::now()->format('Y-m-d H:i:s') . ']' . $query->sql . "\n";
                $content .= '[' . Carbon::now()->format('Y-m-d H:i:s') . ']' . var_export($query->bindings, true) . "\n";
                file_put_contents(storage_path('logs/sql_row.log'), $content, FILE_APPEND);
            });
        }

        //注册Validator验证扩展类
        Validator::resolver(function ($translator, $data, $rules, $messages) {
            return new ExtendValidator($translator, $data, $rules, $messages);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
