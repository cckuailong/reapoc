<?php
/**
 * User: shahnuralam
 * Date: 17/11/18
 * Time: 1:06 AM
 */

namespace WPDM\__;


use WPDM\__\Template;

class UI
{

    static function div($content, $class = '', $attrs = [])
    {
        $class = $class ? " class='{$class}'" : '';
        $_attrs = "";
        foreach ($attrs as $name => $val){
            $_attrs .= " {$name}='$val'";
        }
        return "<div{$class} $_attrs>{$content}</div>";
    }

    static function html($tag, $attrs = [], $content = '')
    {
        $_attrs = "";
        foreach ($attrs as $name => $val){
            $_attrs .= " {$name}='$val'";
        }
        return "<$tag $_attrs>{$content}</$tag>";
    }

    static function a($link, $label = '', $attrs = [])
    {
        $label = $label ? $label : $link;
        $_attrs = "";
        foreach ($attrs as $name => $val){
            $_attrs .= " {$name}='$val'";
        }
        return "<a href='{$link}' $_attrs>{$label}</a>";
    }

    static function button($label, $attrs = []){
        $button = "<button";
        foreach ($attrs as $name => $val){
            $button .= " {$name}='$val'";
        }
        $button .= ">{$label}</button>";
        return $button;
    }

    static function card($heading = '', $content = [], $footer = '', $attrs = []){
        $template = new Template();
        return $template->assign('heading', $heading)
            ->assign('attrs', $attrs)
            ->assign('content', $content)
            ->assign('footer', $footer)
            ->fetch("views/ui-blocks/card.php", __DIR__);
    }

    static function table($thead, $data, $css){
        $template = new Template();
        return $template->assign('thead', $thead)
            ->assign('data', $data)
            ->assign('css', $css)
            ->fetch("views/ui-blocks/table.php", __DIR__);
    }

    static function img($src, $alt = '', $attrs = [])
    {
        $_attrs = "";
        foreach ($attrs as $name => $val){
            $_attrs .= " {$name}='$val'";
        }
        return "<img src='{$src}' alt='{$alt}' {$_attrs} />";
    }

    static function minifyHTML($html)
    {
        $html = str_replace(["\r", "\n"], "", $html);
        return $html;
    }

}
