<?php

/** --------------------------------------------------------------------------------
 * This middleware set the global status of each module. Save the bool data in config
 *
 * [example] config('module.settings_modules_projects')
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Modules;
use Closure;
use Illuminate\Support\Facades\Validator;
use Log;
use Nwidart\Modules\Facades\Module;

class Menus {

    //modules
    private $modules;

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //check if we have any enabled modules
        $this->modules = Module::allEnabled();
        if (count($this->modules) == 0) {
            return $next($request);
        }

        //skip for ajax calls
        if (request()->ajax()) {
            return $next($request);
        }

        //generate menus
        $this->generateMenues();

        //return
        return $next($request);

    }

    /**
     * [generate all menus]
     * generate the html markup for any main menu items for each modules
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generateMenues() {

        //loop through all modules
        foreach ($this->modules as $module) {

            //get all the validated menus for this modules
            $menus = $this->validateMenus($module);

            //[main menu] - do we have main menu items
            if ($menus['main']['state']) {
                $this->generateMainMenu($module, $menus['main']['data'], 'main');
            }

            //[project_tabs menu] - do we have main menu items
            if ($menus['project_tabs']['state']) {
                $this->generateTabsMenu($module, $menus['project_tabs']['data'], 'project_tabs');
            }

        }

    }

    /**
     * [main menu]
     * create the markup for the main menu
     * save to global config
     */
    private function generateMainMenu($module = [], $menus = [], $menu_type = '') {

        //module name
        $module_name = $module->getName();

        //create the HTMLmarkupfor each menu item
        foreach ($menus as $item) {
            //get cleaned array, ready to use
            if ($menu = $this->validateMenu($item, $module, $menu_type)) {

                //create a singlemenu item
                if ($menu['type'] == 'single') {
                    //href
                    $href = (filter_var($menu['data'][0]['href'], FILTER_VALIDATE_URL)) ? $menu['data'][0]['href'] : url($menu['data'][0]['href']);
                    //payload
                    $payload = $menu['data'][0];
                    //generate html
                    $menu_html = view('modules.menus.main.single', compact('payload', 'href'))->render();

                    /** -------------------------------------------------------------------------
                     * Check if this menu is based on a user role.
                     *   [If it is]
                     *   - check if the current user has enough permissions
                     *   [if it is not]
                     *   - add the item to the menu
                     * -------------------------------------------------------------------------*/
                    if (isset($menu['type']) && $menu['type'] == 'role') {
                        if ($this->validateUserRole($menu['role_name'] ?? '', $menu['role_min_value'] ?? '')) {
                            $this->appendToMenu($menu_html, $menu['parent']);
                        }
                    } else {
                        $this->appendToMenu($menu_html, $menu['parent']);
                    }
                }

                //drop down menu
                if ($menu['type'] == 'dropdown') {

                    //validate menu name
                    if (!isset($menu['name']) || (isset($menu['name']) && isset($menu['name']) == '')) {
                        Log::debug("the module [$module_name] has a dropdown menu item with invalid or missing markup [key:name] in config.json file", ['process' => '[modules][menus]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                        //skip this menu item
                        continue;
                    } else {
                        $name = $menu['name'];
                    }

                    //validate menu icon
                    if (!isset($menu['icon']) || (isset($menu['icon']) && isset($menu['icon']) == '')) {
                        Log::debug("the module [$module_name] had a dropdown menu item with invalid or missing markup [key:icon] in config.json file", ['process' => '[modules][menus]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                        //skip this menu item
                        continue;
                    } else {
                        $icon = $menu['icon'];
                    }

                    //each submenu item (hrefs)
                    for ($i = 0; $i < count($menu['data']); $i++) {
                        //href
                        $menu['data'][$i]['href'] = (filter_var($menu['data'][$i]['href'], FILTER_VALIDATE_URL)) ? $menu['data'][$i]['href'] : url($menu['data'][$i]['href']);
                    }

                    //payload
                    $items = $menu['data'];
                    //generate html
                    $menu_html = view('modules.menus.main.dropdown', compact('items', 'name', 'icon'))->render();
                    //append to menu
                    $this->appendToMenu($menu_html, $menu['parent']);
                }

            }
        }
    }

    /**
     * [ menu]
     * create the markup for the main menu
     * save to global config
     */
    private function generateTabsMenu($module = [], $menus = [], $menu_type = '') {

        //module name
        $module_name = $module->getName();

        //create the HTMLmarkupfor each menu item
        foreach ($menus as $item) {
            //get cleaned array, ready to use
            if ($menu = $this->validateMenu($item, $module, $menu_type)) {

                //create a singlemenu item
                if ($menu['type'] == 'single') {
                    //href
                    $href = (filter_var($menu['data'][0]['href'], FILTER_VALIDATE_URL)) ? $menu['data'][0]['href'] : url($menu['data'][0]['href']);

                    //payload
                    $payload = $menu['data'][0];

                    switch ($menu_type) {

                    //project tabs
                    case 'project_tabs':
                        //add contet [id] to the urls
                        $href = str_replace('{id}', request()->route('project'), $href);
                        //content if
                        $payload['id'] = request()->route('project');
                        //create html markup
                        $menu_html = view('modules.menus.project.tabs.single', compact('payload', 'href'))->render();
                        break;

                    }

                    //append to menu
                    if (isset($menu_html)) {
                        $this->appendToMenu($menu_html, $menu['parent']);
                    }
                }

                //drop down menu
                if ($menu['type'] == 'dropdown') {

                    //validate menu name
                    if (!isset($menu['name']) || (isset($menu['name']) && isset($menu['name']) == '')) {
                        Log::debug("the module [$module_name] has a dropdown menu item with invalid or missing markup [key:name] in config.json file", ['process' => '[modules][menus]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                        //skip this menu item
                        continue;
                    } else {
                        $name = $menu['name'];
                    }

                    //each submenu item (hrefs)
                    for ($i = 0; $i < count($menu['data']); $i++) {
                        //href
                        $menu['data'][$i]['href'] = (filter_var($menu['data'][$i]['href'], FILTER_VALIDATE_URL)) ? $menu['data'][$i]['href'] : url($menu['data'][$i]['href']);
                    }

                    //payload
                    $items = $menu['data'];
                    //generate html
                    $menu_html = view('modules.menus.project.tabs.single', compact('items', 'name', 'icon'))->render();
                    //append to menu
                    $this->appendToMenu($menu_html, $menu['parent']);
                }

            }
        }
    }

    /**
     * validate a single menu item for expected array keys
     *  - all menu array keys be present
     *  - put null if no applicable data is needed for the key
     *
     * @return bool
     */
    private function validateMenu($menu = '', $module = '', $menu_type = '') {

        $errors = 0;

        if (!isset($menu['type']) || (isset($menu['type']) && !in_array($menu['type'], ['single', 'dropdown']))) {
            $errors++;
        }

        if (!isset($menu['parent']) || (isset($menu['parent']) && ($menu['parent'] == '' || $menu['parent'] == null))) {
            $errors++;
        }

        if ($errors > 0) {
            Log::debug("the json [menus] markup for menue item [$menu_type]  for the module (" . $module->getName() . ") in the file [config.json] for this modue is invalid or missing some params", ['process' => '[module][menus]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //[lpain url] - create some default data for simplicity
        for ($i = 0; $i < count($menu['data']); $i++) {
            $menu['data'][$i]['type'] = isset($menu['data'][$i]['type']) ? $menu['data'][$i]['type'] : '';
            $menu['data'][$i]['id_li'] = isset($menu['data'][$i]['id_li']) ? $menu['data'][$i]['id_li'] : '';
            $menu['data'][$i]['id_a'] = isset($menu['data'][$i]['id_a']) ? $menu['data'][$i]['id_a'] : '';
            $menu['data'][$i]['classes_li'] = isset($menu['data'][$i]['classes_li']) ? $menu['data'][$i]['classes_li'] : '';
            $menu['data'][$i]['classes_a'] = isset($menu['data'][$i]['classes_a']) ? $menu['data'][$i]['classes_a'] : '';
            $menu['data'][$i]['name'] = isset($menu['data'][$i]['name']) ? $menu['data'][$i]['name'] : '';
            $menu['data'][$i]['href'] = isset($menu['data'][$i]['href']) ? $menu['data'][$i]['href'] : '';
            $menu['data'][$i]['title'] = isset($menu['data'][$i]['title']) ? $menu['data'][$i]['title'] : '';
            $menu['data'][$i]['target'] = isset($menu['data'][$i]['target']) ? $menu['data'][$i]['target'] : '';
            $menu['data'][$i]['icon'] = isset($menu['data'][$i]['icon']) ? $menu['data'][$i]['icon'] : '';
            $menu['data'][$i]['icon'] = isset($menu['data'][$i]['icon']) ? $menu['data'][$i]['icon'] : '';
            $menu['data'][$i]['modal_title'] = isset($menu['data'][$i]['modal_title']) ? $menu['data'][$i]['modal_title'] : '';

        }
        //[modal url] - create some default data for simplicity

        return $menu;

    }

    /**
     * Validate a single module, for following items:
     *  -  the module has a valid config.json file
     *  - check if the file has any menu objects
     *  - return an array with the state and also the menu list (if applicable) for each menu
     *
     * @param  obj  $module
     * @return array
     */
    private function validateMenus($module = []) {

        //module path
        $module_path = $module->getPath();

        //module name
        $module_name = $module->getName();

        //mdule json file
        $module_jason_file = $module_path . '/config.json';

        //valid menu types that we expect to find in the modules config.json file
        $menus = [
            'main' => [
                'state' => false,
                'data' => [],
            ],
            'top' => [
                'state' => false,
                'data' => [],
            ],
            'projects_actions' => [
                'state' => false,
                'data' => [],
            ],
            'project_tabs' => [
                'state' => false,
                'data' => [],
            ],
            'project_actions' => [
                'state' => false,
                'data' => [],
            ],
            'clients_actions' => [
                'state' => false,
                'data' => [],
            ],
            'client_tabs' => [
                'state' => false,
                'data' => [],
            ],
            'client_actions' => [
                'state' => false,
                'data' => [],
            ],
            'tasks_actions' => [
                'state' => false,
                'data' => [],
            ],
            'leads_actions' => [
                'state' => false,
                'data' => [],
            ],
            'invoices_actions' => [
                'state' => false,
                'data' => [],
            ],
            'invoice_actions' => [
                'state' => false,
                'data' => [],
            ],
            'estimates_actions' => [
                'state' => false,
                'data' => [],
            ],
            'estimate_actions' => [
                'state' => false,
                'data' => [],
            ],
            'items_actions' => [
                'state' => false,
                'data' => [],
            ],
            'tickets' => [
                'state' => false,
                'data' => [],
            ],
            'ticket' => [
                'state' => false,
                'data' => [],
            ],
        ];

        if (!is_file($module_jason_file)) {
            Log::debug("the modules [$module_name] does not have a valid (config.json) file", ['process' => '[modules][menus]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        //validate json file
        $module_config = json_decode(file_get_contents($module_jason_file), true);

        //check if we have any main menu items at all
        if (isset($module_config['menus'])) {
            foreach ($menus as $name => $value) {
                if (isset($module_config['menus'][$name])) {
                    if (is_array($module_config['menus'][$name]) && count($module_config['menus'][$name]) > 0) {

                        //validate this menu's json structure
                        $menus[$name]['state'] = $this->valiateMenu($module_config['menus'][$name]);
                        $menus[$name]['data'] = $module_config['menus'][$name];
                    }
                }
            }
        }

        //return
        return $menus;
    }

    /**
     * Validate menus
     * [TODO]
     */
    private function valiateMenu($menu = '') {


        //for now just return TRUE;
        return true;


        //default state
        $state = true;

        for ($i = 0; $i < count($menu); $i++) {
            $validator = Validator::make($menu[$i], [
                'title' => 'required',
                'parent' => 'required',
                'user_role' => 'required',
                'role_name' => 'required',
                'role_min_value' => 'nullable|integer',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                $state = false;
                Log::debug("the modules [$module_name] has an invalid [menu] item, in (config.json)", ['process' => '[modules][menus]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'error' => $errors]);
                continue;
            }
        }

        //return state
        return $state;

    }

    /**
     * [main menu]
     * add the HTML code to the correct parent
     * save to global config
     */
    private function appendToMenu($menu_html = '', $parent = '') {

        if ($parent != '') {
            if (config()->has("module_menus.$parent")) {
                $current = config("module_menus.$parent");
                $menu_html = $current . $menu_html;
            }
            config(["module_menus.$parent" => $menu_html]);
        }

    }

    /**
     * [main menu]
     * add the HTML code to the correct parent
     * save to global config
     */
    private function urlResourceID($url = '', $resource = '') {

        //set the correct ID
        switch ($resource) {

        case 'project':
            $id = request()->route('project');
            break;

        default:
            $id = 0;
            break;

        }

        return preg_replace('/{id}/', $id, $url);
    }

}