<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use App\Models\Category;
use App\Models\Tour;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production to avoid mixed-content when behind proxies (e.g., Railway)
        if (app()->environment('production')) {
            URL::forceScheme('https'); 
        }

        // Chia sẻ danh sách category cho mọi view (dùng cache đơn giản tránh query lặp lại)
        View::composer('*', function ($view) {
            static $sharedCategories = null;
            static $domesticCategories = null;
            static $internationalCategories = null;

            if ($sharedCategories === null) {
                $sharedCategories = Category::orderBy('categoryName')->get(['categoryID', 'categoryName']);
            }

            if ($domesticCategories === null || $internationalCategories === null) {
                $tourTable = (new Tour())->getTable();
                $columns = [];
                try {
                    $columns = Schema::getColumnListing($tourTable);
                } catch (\Throwable $e) {
                    $columns = [];
                }
                $lowerCols = array_map('strtolower', $columns);
                $candidates = ['type', 'tourtype', 'tour_type'];
                $foundIndex = null;
                foreach ($candidates as $cand) {
                    $idx = array_search($cand, $lowerCols, true);
                    if ($idx !== false) {
                        $foundIndex = $idx;
                        break;
                    }
                }
                $typeColumn = $foundIndex !== null ? $columns[$foundIndex] : null;

                if ($typeColumn) {
                    $domesticCatIds = Tour::select('categoryID')
                        ->whereRaw("LOWER(TRIM(`$typeColumn`)) = ?", ['domestic'])
                        ->distinct()->pluck('categoryID')->all();
                    $internationalCatIds = Tour::select('categoryID')
                        ->whereRaw("LOWER(TRIM(`$typeColumn`)) = ?", ['international'])
                        ->distinct()->pluck('categoryID')->all();

                    $domesticCategories = Category::whereIn('categoryID', $domesticCatIds)
                        ->orderBy('categoryName')->get(['categoryID', 'categoryName']);
                    $internationalCategories = Category::whereIn('categoryID', $internationalCatIds)
                        ->orderBy('categoryName')->get(['categoryID', 'categoryName']);
                } else {
                    $domesticCategories = collect();
                    $internationalCategories = collect();
                }
            }

            $view->with('sharedCategories', $sharedCategories)
                ->with('domesticCategories', $domesticCategories)
                ->with('internationalCategories', $internationalCategories);
        });
    }
}
