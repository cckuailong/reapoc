<?php


namespace WPDM\Admin;


use WPDM\__\FileSystem;

class PackageTemplate
{
    public $templateType = 'link';
    private $dir;

    function __construct($templateType = 'link')
    {
        $this->templateType = $templateType;
        $this->dir = WP_CONTENT_DIR . "/wpdm-assets/{$templateType}-templates/";
        FileSystem::mkDir($this->dir, 0755, true);
    }

    function type($templateType)
    {
        $templateType = !in_array($templateType, ['link', 'page']) ? 'link' : $templateType;
        $this->templateType = $templateType;
        $this->dir = WP_CONTENT_DIR . "/wpdm-assets/{$templateType}-templates/";
        FileSystem::mkDir($this->dir, 0755, true);
        return $this;
    }

    function covertAll($templateType = null)
    {
        if ($templateType !== null)
            $this->type($templateType);
        if(!file_exists($this->dir)){
            \WPDM\__\Messages::error("Failed to create template directory ( {$this->dir} )!", 0, 'notify');
            return $this;
        }
        $tpldata = get_option("_fm_{$this->templateType}_templates", false);
        if (!is_array($tpldata))
            $tpldata = maybe_unserialize($tpldata);
        if ($tpldata) {
            delete_option("_fm_{$this->templateType}_templates");
            foreach ($tpldata as $id => $tpl) {
                $file = $this->dir . '/' . $id . '.xml';
                $tpl['content'] = stripslashes_deep($tpl['content']);
                $tpl['css'] = stripslashes_deep($tpl['css']);
                $data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><template><tplid>{$id}</tplid><name>{$tpl['title']}</name><designer>Admin</designer><code><![CDATA[{$tpl['content']}]]></code><css><![CDATA[{$tpl['css']}]]></css></template>";
                file_put_contents($file, $data);

            }
        }
        return $this;
    }

    function import($url, $templateType = null)
    {

        if ($templateType !== null)
            $this->type($templateType);
        $data = wpdm_remote_get($url);
        $local_file_path = $this->dir . '/' . basename($url);
        file_put_contents($local_file_path, $data);

    }

    function add($id, $name, $code, $css = '', $templateType = null)
    {
        if ($templateType !== null)
            $this->type($templateType);

        $file = $this->dir . '/' . $id . '.xml';
        $code = stripslashes_deep($code);
        $data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><template><tplid>{$id}</tplid><name>{$name}</name><designer>Admin</designer><code><![CDATA[{$code}]]></code><css><![CDATA[{$css}]]></css></template>";
        file_put_contents($file, $data);
        return $this;
    }

    function delete($id, $templateType = null)
    {
        if ($templateType !== null)
            $this->type($templateType);

        $id = wpdm_sanitize_var($id, 'filename');
        if ($id && file_exists($this->dir . $id . '.xml'))
            @unlink($this->dir . $id . '.xml');

        return $this;
    }

    function get($template, $templateType = null, $contentOnly = false)
    {
        if ($templateType !== null)
            $this->type($templateType);
        $template = str_replace(".xml", "", $template);
        $file = $this->dir . $template . '.xml';
        $tpl = [];
        if (file_exists($file)) {
            $data = file_get_contents($file);
            $ddoc = new \DOMDocument();
            $valid = $ddoc->loadXML($data);
            if(!$valid || !is_object($ddoc->getElementsByTagName('code')->item(0))) return false;
            $tpl = [];
            $tpl['ID'] = $ddoc->getElementsByTagName('tplid')->item(0)->textContent;
            $tpl['name'] = $ddoc->getElementsByTagName('name')->item(0)->textContent;
            $tpl['content'] = $ddoc->getElementsByTagName('code')->item(0)->textContent;
            $tpl['css'] = $ddoc->getElementsByTagName('css')->item(0)->textContent;
            $tpl['file'] = $file;
            $add_styles = $this->templateType === 'page' ? "<style>{$tpl['css']}</style>" : '';
            return $contentOnly ? $tpl['content'] . $add_styles : $tpl;
        }
        return false;
    }

    function getCustomTemplates($templateType = null)
    {
        if ($templateType !== null)
            $this->type($templateType);
        $_templates = FileSystem::scanDir($this->dir, false, false, '.xml');
        $templates = [];
        foreach ($_templates as $tplid) {
            $tplid = str_replace(".xml", "", $tplid);
            $template = $this->get($tplid, $this->templateType);
            $templates[$tplid] = $template;
        }
        return $templates;
    }


    function getTemplates($templateType = null, $activeOnly = false)
    {
        if ($templateType !== null)
            $this->type($templateType);

        $tplstatus = maybe_unserialize(get_option("_fm_{$this->templateType}_template_status"));

        $xactivetpls = array();
        $activetpls = array();
        if (is_array($tplstatus)) {
            foreach ($tplstatus as $tpl => $active) {
                if (!$active)
                    $xactivetpls[] = $tpl;
                else
                    $activetpls[] = $tpl;
            }
        }

        $all_templates = array();


        //Load custom templates
        $custom_templates = $this->getCustomTemplates();
        $all_templates = $custom_templates;

        //Load all core template provided with the plugin
        $core_templates_dir = WPDM()->package->templateDir . $this->templateType . '-templates/';
        $core_templates = FileSystem::scanDir($core_templates_dir, false, true, '.php');
        foreach ($core_templates as $core_template) {
            $all_templates[basename($core_template)] = $core_template;
        }

        //Load all template from parent theme, override core template
        if (get_stylesheet_directory() !== get_template_directory()) {
            $parent_theme_dir = get_template_directory() . '/download-manager/' . $this->templateType . '-templates/';
            $parent_theme_templates = FileSystem::scanDir($parent_theme_dir, false, true, '.php');
            foreach ($parent_theme_templates as $theme_template) {
                $all_templates[basename($theme_template)] = $theme_template;
            }
        }

        //Load all template from active theme, override core or parent template
        $theme_dir = get_stylesheet_directory() . '/download-manager/' . $this->templateType . '-templates/';
        $theme_templates = FileSystem::scanDir($theme_dir, false, true, '.php');
        foreach ($theme_templates as $theme_template) {
            $all_templates[basename($theme_template)] = $theme_template;
        }

        return $all_templates;

    }

    function getStyles($templateType = null)
    {
        if ($templateType !== null)
            $this->type($templateType);
        $styles = []; //TempStorage::get('__wpdm_custom_link_template_styles');
        //if( $styles !== '' ) return $styles;
        $templates = $this->getCustomTemplates($this->templateType);

        foreach ($templates as $template) {
            $styles[$template['ID']] = stripslashes_deep($template['css']);
        }
        $styles = implode("\r\n", $styles);
        //TempStorage::set("__wpdm_custom_link_template_styles", $styles);
        //wpdmdd($styles);
        return $styles;
    }

    function dropdown($params, $activeOnly = false)
    {
        extract($params);
        $type = isset($type) && in_array($type, array('link', 'page', 'email')) ? esc_attr($type) : 'link';
        $tplstatus = maybe_unserialize(get_option("_fm_{$type}_template_status"));

        $xactivetpls = array();
        $activetpls = array();
        if (is_array($tplstatus)) {
            foreach ($tplstatus as $tpl => $active) {
                if (!$active)
                    $xactivetpls[] = $tpl;
                else
                    $activetpls[] = $tpl;
            }
        }


        $templates = $this->getTemplates($type);

        //Field Name
        $name = isset($name) ? $name : $type . '_template';

        //Field css
        $css = isset($css) ? "style='$css'" : '';

        $class = isset($class) ? $class : '';

        //Field ID
        $id = isset($id) ? $id : uniqid();

        //Default value
        $default = $type == 'link' ? 'link-template-default.php' : 'page-template-default.php';
        $xdf = str_replace(".php", "", $default);
        if (is_array($xactivetpls) && count($xactivetpls) > 0)
            $default = (in_array($xdf, $xactivetpls) || in_array($default, $xactivetpls)) && isset($activetpls[0]) ? $activetpls[0] : $default;

        $html = "<select name='$name' id='$id' class='form-control {$class} template {$type}_template' {$css}><option value='$default'>Select " . ucfirst($type) . " Template</option>";
        $data = array();
        if (is_array($templates)) {
            foreach ($templates as $id => $template) {
                $name = null;
                $_id = str_replace(".php", "", $id);
                if (!$activeOnly || ($activeOnly && (!isset($tplstatus[$_id]) || (int)$tplstatus[$_id] === 1))) {
                    if (!is_array($template) && $template) {
                        $tmpdata = file_get_contents($template);
                        $regx = "/WPDM.*Template[\s]*:([^\-\->]+)/";
                        if (preg_match($regx, $tmpdata, $matches)) {
                            $name = $matches[1];
                        } else continue;
                    } else {
                        $name = $template['name'];
                    }
                    if ($name) {
                        $eselected = isset($selected) && $selected == $id ? 'selected=selected' : '';
                        $html .= "<option value='{$id}' {$eselected}>{$name}</option>";
                    }
                }
            }
        }

        $html .= "</select>";

        return $html;
    }

}
