<?php
/** ----------------------------------------------------------------------------------------
 *  [GROWCRM][THEME SERVICE PROVIDER]
 *
 *  -Sets and validate the correct theme (as set in the database)
 *
 *  This service provider is skipped when the application's setup has not been completed
 * -----------------------------------------------------------------------------------------*/

/** --------------------------------------------------------------------------------
 * This service provider configures the applications email settings
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Providers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Log;

class ConfigThemeServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {

        //do not run this for SETUP path
        if (env('SETUP_STATUS') != 'COMPLETED') {
            //set default theme
            config([
                'theme.selected_theme_css' => 'public/themes/default/css/style.css?v=1',
            ]);
            //skip this provider
            return;
        }

        //get settings
        $settings = \App\Models\Settings::find(1);

        //get all directories in themes folder
        $directories = Storage::disk('root')->directories('public/themes');

        //clean up directory names
        array_walk($directories, function (&$value, &$key) {
            $value = str_replace('public/themes/', '', $value);
        });

        //check if default theme exists
        if (!in_array($settings->settings_theme_name, $directories)) {
            Log::critical("The selected theme directory could not be found", ['process' => '[validating theme]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'Theme Directory: ' => '/public/themes/' . $settings->settings_theme_name]);
            abort(409, __('lang.default_theme_not_found') . ' [' . runtimeThemeName($settings->settings_theme_name) . ']');
        }

        //check if css file exists
        if (!is_file(BASE_DIR . '/public/themes/' . $settings->settings_theme_name . '/css/style.css')) {
            Log::critical("The selected theme does not seem to have a style.css files", ['process' => '[validating theme]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'Theme Directory: ' => '/public/themes/' . $settings->settings_theme_name]);
            abort(409, __('lang.selected_theme_is_invalid'));
        }

        //validate if the folders in the /public/themes/ directory have a style.css file
        $list = [];
        foreach ($directories as $directory) {
            if (is_file(BASE_DIR . "/public/themes/$directory/css/style.css")) {
                $list[] = $directory;
            }
        }

        //set global theme (used for users who are not logged in)
        config([
            'theme.list' => $list,
            'theme.selected_name' => $settings->settings_theme_name,
            //main css file
            'theme.selected_theme_css' => 'public/themes/' . $settings->settings_theme_name . '/css/style.css?v=' . $settings->settings_system_javascript_versioning,
            //invoice/estimate pdf (web preview)
            //[8 Aug 2021] all themes should now use the 'default' theme's bill-pdf.css file (public/themes/default/css/bill-pdf.css)
            'theme.selected_theme_pdf_css' => 'public/themes/default/css/bill-pdf.css?v=' . $settings->settings_system_javascript_versioning,
        ]);

        //[user custom theme] - set the theme for the current user (apply to all views)
        view()->composer('*', function ($view) {
            if (auth()->check()) {
                //validate current theme
                if (!is_file(BASE_DIR . '/public/themes/' . auth()->user()->pref_theme . '/css/style.css')) {
                    //set use to default system theme
                    auth()->user()->pref_theme = $settings->settings_theme_name;
                    auth()->user()->save();
                }
            }
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        //
    }

}
