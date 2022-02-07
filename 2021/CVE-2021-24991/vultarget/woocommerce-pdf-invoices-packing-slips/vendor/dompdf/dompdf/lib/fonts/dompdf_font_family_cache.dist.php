<?php
// $distFontDir = $rootDir . '/lib/fonts'; // should work fine too?
$distFontDir = trailingslashit( WPO_WCPDF()->plugin_path() ) . 'vendor' . DIRECTORY_SEPARATOR . 'dompdf' . DIRECTORY_SEPARATOR . 'dompdf' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR;

return array(
    'sans-serif' =>
        array(
            'normal' => $distFontDir . 'Helvetica',
            'bold' => $distFontDir . 'Helvetica-Bold',
            'italic' => $distFontDir . 'Helvetica-Oblique',
            'bold_italic' => $distFontDir . 'Helvetica-BoldOblique'
        ),
    'times' =>
        array(
            'normal' => $distFontDir . 'Times-Roman',
            'bold' => $distFontDir . 'Times-Bold',
            'italic' => $distFontDir . 'Times-Italic',
            'bold_italic' => $distFontDir . 'Times-BoldItalic'
        ),
    'times-roman' =>
        array(
            'normal' => $distFontDir . 'Times-Roman',
            'bold' => $distFontDir . 'Times-Bold',
            'italic' => $distFontDir . 'Times-Italic',
            'bold_italic' => $distFontDir . 'Times-BoldItalic'
        ),
    'courier' =>
        array(
            'normal' => $distFontDir . 'Courier',
            'bold' => $distFontDir . 'Courier-Bold',
            'italic' => $distFontDir . 'Courier-Oblique',
            'bold_italic' => $distFontDir . 'Courier-BoldOblique'
        ),
    'helvetica' =>
        array(
            'normal' => $distFontDir . 'Helvetica',
            'bold' => $distFontDir . 'Helvetica-Bold',
            'italic' => $distFontDir . 'Helvetica-Oblique',
            'bold_italic' => $distFontDir . 'Helvetica-BoldOblique'
        ),
    'zapfdingbats' =>
        array(
            'normal' => $distFontDir . 'ZapfDingbats',
            'bold' => $distFontDir . 'ZapfDingbats',
            'italic' => $distFontDir . 'ZapfDingbats',
            'bold_italic' => $distFontDir . 'ZapfDingbats'
        ),
    'symbol' =>
        array(
            'normal' => $distFontDir . 'Symbol',
            'bold' => $distFontDir . 'Symbol',
            'italic' => $distFontDir . 'Symbol',
            'bold_italic' => $distFontDir . 'Symbol'
        ),
    'serif' =>
        array(
            'normal' => $distFontDir . 'Times-Roman',
            'bold' => $distFontDir . 'Times-Bold',
            'italic' => $distFontDir . 'Times-Italic',
            'bold_italic' => $distFontDir . 'Times-BoldItalic'
        ),
    'monospace' =>
        array(
            'normal' => $distFontDir . 'Courier',
            'bold' => $distFontDir . 'Courier-Bold',
            'italic' => $distFontDir . 'Courier-Oblique',
            'bold_italic' => $distFontDir . 'Courier-BoldOblique'
        ),
    'fixed' =>
        array(
            'normal' => $distFontDir . 'Courier',
            'bold' => $distFontDir . 'Courier-Bold',
            'italic' => $distFontDir . 'Courier-Oblique',
            'bold_italic' => $distFontDir . 'Courier-BoldOblique'
        ),
    'dejavu sans' =>
        array(
            'bold' => $distFontDir . 'DejaVuSans-Bold',
            'bold_italic' => $distFontDir . 'DejaVuSans-BoldOblique',
            'italic' => $distFontDir . 'DejaVuSans-Oblique',
            'normal' => $distFontDir . 'DejaVuSans'
        ),
    'dejavu sans mono' =>
        array(
            'bold' => $distFontDir . 'DejaVuSansMono-Bold',
            'bold_italic' => $distFontDir . 'DejaVuSansMono-BoldOblique',
            'italic' => $distFontDir . 'DejaVuSansMono-Oblique',
            'normal' => $distFontDir . 'DejaVuSansMono'
        ),
    'dejavu serif' =>
        array(
            'bold' => $distFontDir . 'DejaVuSerif-Bold',
            'bold_italic' => $distFontDir . 'DejaVuSerif-BoldItalic',
            'italic' => $distFontDir . 'DejaVuSerif-Italic',
            'normal' => $distFontDir . 'DejaVuSerif'
        ),
    'open sans' => 
        array(
        'normal' => $distFontDir . 'OpenSans-Normal',
        'bold' => $distFontDir . 'OpenSans-Bold',
        'italic' => $distFontDir . 'OpenSans-Italic',
        'bold_italic' => $distFontDir . 'OpenSans-BoldItalic',
    ),
    'segoe' => 
        array(
            'normal' => $distFontDir . 'Segoe-Normal',
            'bold' => $distFontDir . 'Segoe-Bold',
            'italic' => $distFontDir . 'Segoe-Italic',
            'bold_italic' => $distFontDir . 'Segoe-BoldItalic',
        ),
    'roboto slab' => 
        array(
            'normal' => $distFontDir . 'RobotoSlab-Normal',
            'bold' => $distFontDir . 'RobotoSlab-Bold',
            'italic' => $distFontDir . 'RobotoSlab-Italic',
            'bold_italic' => $distFontDir . 'RobotoSlab-BoldItalic',
        ),
    'currencies' => 
        array(
            'normal' => $distFontDir . 'currencies',
            'bold' => $distFontDir . 'currencies',
            'italic' => $distFontDir . 'currencies',
            'bold_italic' => $distFontDir . 'currencies',
        ),
)
?>