<?php
if (!defined('WPINC')) {
    die('Closed');
}
$form->addElement(new Element_Hidden("field_id",rand(10,50000)));
$form->addElement(new Element_Hidden("page_no",1));
$form->addElement(new Element_Hidden("is_field_primary",0));