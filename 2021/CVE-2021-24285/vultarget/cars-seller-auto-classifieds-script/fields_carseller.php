<?php


require_once("meta-box-class/my-meta-box-class.php");


/*
* configure your meta box
*/
$config = array(
    'id' => 'carsellers-box',             // meta box id, unique per meta box 
    'title' => '<h1>Car feather options</h1>',      // meta box title
    'pages' => array('carsellers'),    // post types, accept custom post types as well, default is array('post'); optional
    'context' => 'normal',               // where the meta box appear: normal (default), advanced, side; optional
    'priority' => 'high',                // order of meta box: high (default), low; optional
    'fields' => array(),                 // list of meta fields (can be added by field arrays) or using the class's functions
    'local_images' => false,             // Use local or hosted images (meta box images for add/remove)
    'use_with_theme' => false            //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
);

/*
* Initiate your meta box
*/
$my_meta = new AT_Meta_Box($config);

$prefix="car_";
/*
* Add fields to your meta box
*/
$my_meta->addWysiwyg($prefix.'Vehicle_Description',array('name'=> 'Vehicle Description '));

$my_meta->addNumber($prefix.'our_price',array('name'=> 'Asking Price:'));
$my_meta->addText($prefix.'address',array('name'=> 'Address:'));
$my_meta->addText($prefix.'model',array('name'=> 'Model: '));
$my_meta->addText($prefix.'registration_date',array('name'=> 'Registration date: '));
$my_meta->addText($prefix.'running_status',array('name'=> 'Running Status: '));
$my_meta->addText($prefix.'ownership',array('name'=> 'Ownership: '));
$my_meta->addText($prefix.'insurance_status',array('name'=> 'Insurance Status: '));
$my_meta->addText($prefix.'fuel_type',array('name'=> 'Fuel Type: '));
$my_meta->addText($prefix.'mileage',array('name'=> 'Mileage:'));
$my_meta->addText($prefix.'condition',array('name'=> 'Condition:'));
$my_meta->addText($prefix.'exterior_color',array('name'=> 'Exterior Color:'));
$my_meta->addText($prefix.'interior_color',array('name'=> 'Interior Color:'));
$my_meta->addText($prefix.'transmission',array('name'=> 'Transmission:'));
$my_meta->addText($prefix.'engine',array('name'=> 'Engine:'));






/*
//text field
$my_meta->addText($prefix.'text_field_id',array('name'=> 'My Text '));
//textarea field
$my_meta->addTextarea($prefix.'textarea_field_id',array('name'=> 'My Textarea '));
//checkbox field
$my_meta->addCheckbox($prefix.'checkbox_field_id',array('name'=> 'My Checkbox '));
//select field
$my_meta->addSelect($prefix.'select_field_id',array('selectkey1'=>'Select Value1','selectkey2'=>'Select Value2'),array('name'=> 'My select ', 'std'=> array('selectkey2')));
//radio field
$my_meta->addRadio($prefix.'radio_field_id',array('radiokey1'=>'Radio Value1','radiokey2'=>'Radio Value2'),array('name'=> 'My Radio Filed', 'std'=> array('radionkey2')));
//date field
$my_meta->addDate($prefix.'date_field_id',array('name'=> 'My Date '));
//Time field
$my_meta->addTime($prefix.'time_field_id',array('name'=> 'My Time '));
//Color field
$my_meta->addColor($prefix.'color_field_id',array('name'=> 'My Color '));
//Image field
$my_meta->addImage($prefix.'image_field_id',array('name'=> 'My Image '));
//file upload field
$my_meta->addFile($prefix.'file_field_id',array('name'=> 'My File '));
//wysiwyg field
$my_meta->addWysiwyg($prefix.'wysiwyg_field_id',array('name'=> 'My wysiwyg Editor '));
//taxonomy field
$my_meta->addTaxonomy($prefix.'taxonomy_field_id',array('taxonomy' => 'category'),array('name'=> 'My Taxonomy '));
//posts field
$my_meta->addPosts($prefix.'posts_field_id',array('post_type' => 'post'),array('name'=> 'My Posts '));
 
/*
* To Create a reapeater Block first create an array of fields
* use the same functions as above but add true as a last param
*/

/*$repeater_fields[] = $my_meta->addText($prefix.'re_text_field_id',array('name'=> 'My Text '),true);
$repeater_fields[] = $my_meta->addTextarea($prefix.'re_textarea_field_id',array('name'=> 'My Textarea '),true);
$repeater_fields[] = $my_meta->addCheckbox($prefix.'re_checkbox_field_id',array('name'=> 'My Checkbox '),true);*/
$repeater_fields[] = $my_meta->addImage($prefix.'carsellers_image',array('name'=> 'carseller Image '),true);
 
/*
* Then just add the fields to the repeater block
*/
//repeater block
$my_meta->addRepeaterBlock($prefix.'carsellers_images',array('inline' => true, 'name' => 'carseller Images','fields' => $repeater_fields));


/*
* Don't Forget to Close up the meta box deceleration
*/
//Finish Meta Box Deceleration
$my_meta->Finish();
