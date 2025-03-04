<?php
/**
 * Controller Module D.Menu Editor Class
 *
 * @version 1.0
 * 
 * @author D.art <d.art.reply@gmail.com>
 */

class ControllerExtensionModuleDMenuEditorEvents extends Controller {

    /**
     * Index.
     * 
     * @return string
     */
    public function index() {
        return '';
    }

    /**
     * Event trigger: catalog/controller/common/menu/before
     *
     * @param  string $route
     * @param  array $data
     *
     * @return void
     */
    public function catalogControllerMenuBefore(&$route, &$data) {
        // D.Menu Editor Settings.
        if ($this->config->get('module_dmenu_editor_settings')) {
            $module_dmenu_editor_settings = $this->config->get('module_dmenu_editor_settings');
        } else {
            $module_dmenu_editor_settings = array();
        }

        // D.Menu Editor Main Menu.
        if ($this->config->get('module_dmenu_editor_status') && $module_dmenu_editor_settings['menu']['main']['status']) {
            // Route.
            $route = 'extension/module/dmenu_editor';

            // Menu type.
            $data['menu_type'] = 'main';
        }
    }

    /**
     * Event trigger: catalog/view/common/currency/before
     *
     * @param  string $route
     * @param  array $data
	 * @param  string $code
     *
     * @return void
     */
    public function catalogViewCurrencyBefore(&$route, &$data, &$code = '') {
        // D.Menu Editor Settings.
        if ($this->config->get('module_dmenu_editor_settings')) {
            $module_dmenu_editor_settings = $this->config->get('module_dmenu_editor_settings');
        } else {
            $module_dmenu_editor_settings = array();
        }

        // Top Menu Currency template (twig).
        if ($this->config->get('module_dmenu_editor_status') && $module_dmenu_editor_settings['menu']['top']['status']) {
            $this->load->language('extension/module/dmenu_editor');

            // Route.
            $route = 'extension/module/dmenu_editor/currency';

            // Translated Text.
            $data['translated_text'] = array(
                'text_back' => $this->language->get('text_back'),
                'text_all'  => $this->language->get('text_all')
            );
        }
    }

    /**
     * Event trigger: catalog/view/common/language/before
     *
     * @param  string $route
     * @param  array $data
	 * @param  string $code
     *
     * @return void
     */
    public function catalogViewLanguageBefore(&$route, &$data, &$code = '') {
        // D.Menu Editor Settings.
        if ($this->config->get('module_dmenu_editor_settings')) {
            $module_dmenu_editor_settings = $this->config->get('module_dmenu_editor_settings');
        } else {
            $module_dmenu_editor_settings = array();
        }

        // Top Menu Language template (twig).
        if ($this->config->get('module_dmenu_editor_status') && $module_dmenu_editor_settings['menu']['top']['status']) {
            $this->load->language('extension/module/dmenu_editor');

            // Route.
            $route = 'extension/module/dmenu_editor/language';

            // Translated Text.
            $data['translated_text'] = array(
                'text_back' => $this->language->get('text_back'),
                'text_all'  => $this->language->get('text_all')
            );
        }
    }

    /**
     * Event trigger: catalog/view/common/header/after
     *
     * @param  string $route
     * @param  array $data
     * @param  string $output
     * 
     * @return void
     */
    public function catalogViewHeaderAfter(&$route, &$data, &$output) {
        if ($this->config->get('module_dmenu_editor_status')) {
            // D.Menu Editor Settings.
            if ($this->config->get('module_dmenu_editor_settings')) {
                $module_dmenu_editor_settings = $this->config->get('module_dmenu_editor_settings');
                $module_dmenu_editor_settings_close = false;

                foreach ($module_dmenu_editor_settings as $key => $setting) {
                    if ($key == 'menu') {
                        foreach ($setting as $menu) {
                            if ($menu['status'] && $menu['close']) {
                                $module_dmenu_editor_settings_close = true;
                            }
                        }

                        break;
                    }
                }
            } else {
                $module_dmenu_editor_settings = array();
                $module_dmenu_editor_settings_close = false;
            }

            // New HTML.
            $html  = '';
            $html .= '<link href="' . HTTP_SERVER . 'catalog/view/javascript/module-dmenu_editor/dmenu_editor.css" type="text/css" rel="stylesheet" media="screen">';
            $html .= '<script src="' . HTTP_SERVER . 'catalog/view/javascript/module-dmenu_editor/dmenu_editor.js" type="text/javascript"></script>';

            if ($module_dmenu_editor_settings_close) {
                $html .= '<script src="' . HTTP_SERVER . 'catalog/view/javascript/module-dmenu_editor/touchSwipe/jquery.touchSwipe.min.js" type="text/javascript"></script>';
            }

            // Find HTML.
            $find = '</head>';

            // Replace HTML.
            $replace = $html . $find;

            // Replace Output.
            $output = str_replace($find, $replace, $output);

            // Top Menu.
            if ($this->config->get('module_dmenu_editor_status') && $module_dmenu_editor_settings['menu']['top']['status']) {
                $dmenu_top = $this->load->controller('extension/module/dmenu_editor', array('menu_type' => 'top'));

                // Operation 1.
                // Add D.Menu Top Menu.
                $html = '<div id="dmenu_editor-top">' . $dmenu_top . '</div>';
                $id = 'top'; // Element ID
                $find = '/(<.*?id=["\']' . $id . '["\'].*?>)/i';
                $replace = $html . '$1';
                $output = preg_replace($find, $replace, $output);

                // Operation 2.
                // Hide Current Theme Top Menu.
                $id = 'top'; // Element ID
                $find = '/<(\w*)\s+.*?id=["\']' . $id . '["\'].*?>/i';
                $replace = '<$1 id="' . $id . '" style="display: none !important;">';
                $output = preg_replace($find, $replace, $output);

                // Operation 2 Alt.
                // Remove Current Theme Top Menu. Remove HTML-content by ID.
                //$this->load->model('extension/module/dmenu_editor');
                //$output = $this->model_extension_module_dmenu_editor->removeTagsByID($output, array('top'));
            }
        }
    }

    /**
     * Event trigger: catalog/view/common/footer/after
     *
     * @param  string $route
     * @param  array $data
     * @param  string $output
     *
     * @return void
     */
    public function catalogViewFooterAfter(&$route, &$data, &$output) {
        // D.Menu Editor Settings.
        if ($this->config->get('module_dmenu_editor_settings')) {
            $module_dmenu_editor_settings = $this->config->get('module_dmenu_editor_settings');
        } else {
            $module_dmenu_editor_settings = array();
        }

        // Footer Menu.
        if ($this->config->get('module_dmenu_editor_status') && $module_dmenu_editor_settings['menu']['footer']['status']) {
            $dmenu_footer = $this->load->controller('extension/module/dmenu_editor', array('menu_type' => 'footer'));

            // Operation.
            $html = '<div id="dmenu_editor-footer">' . $dmenu_footer . '</div>';
            $find = '/(<footer.*?>)/i';
            $replace = '$1' . $html;
            $output = preg_replace($find, $replace, $output);
        }

        // Social Menu.
        if ($this->config->get('module_dmenu_editor_status') && $module_dmenu_editor_settings['menu']['social']['status']) {
            $dmenu_social = $this->load->controller('extension/module/dmenu_editor', array('menu_type' => 'social'));

            // Operation.
            $html = '<div id="dmenu_editor-social">' . $dmenu_social . '</div>';
            $find = '</footer>';
            $replace = $html . $find;
            $output = str_replace($find, $replace, $output);
        }
    }
}