<?php

namespace Padosoft\Laravel\ActivitylogExtended;

use Illuminate\Support\ServiceProvider;

class ActivitylogExtendedServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        if (!class_exists('EnhanceActivityLogTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__ . '/../migrations/enhance_activity_log_table.php.stub' => database_path("/migrations/{$timestamp}_enhance_activity_log_table.php"),
            ], 'migrations');
        }
    }


}
