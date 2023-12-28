<?php

namespace App\Providers;

use App\Model\AboutUsModel;
use App\Model\CategoryModel;
use App\Model\SeoDataModel;
use App\Model\WebInfoModel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
      URL::forceScheme('https');
        $categories_list =CategoryModel::where('parent_id', 0)->where('status', 'Active')->orderByRaw('ISNULL(priority), priority ASC')->get();
        View::share('categories',$categories_list);
        $footer_description = AboutUsModel::where('id',2)->value('description');
        View::share('footer_description',$footer_description);

        $footer_categories = CategoryModel::where('parent_id',0)->where('status','Active')->inRandomOrder()->limit(5)->get();
        View::share('footer_categories',$footer_categories);
        $segement= \Request::segment(1);
        $seo_data = SeoDataModel::where("page_slug",$segement)->first();
        $use_title = false;
        if(empty($seo_data)){
            $seo_data =  SeoDataModel::where("page_slug","home")->first();
            $use_title = true;
        }
        View::share('seo_data',$seo_data);
        View::share('use_title',$use_title);
        $webDataList = WebInfoModel::get();
        foreach ($webDataList as $row){
            $webData[$row->key] =  $row->value;
        }
        View::share('webData',$webData);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}
