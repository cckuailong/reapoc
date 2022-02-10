<?php
cmplz_notice( _x( "The script center should be used to add and block third-party scripts and iFrames before consent is given, or when consent is revoked. For example Hotjar and embedded video’s.",
'intro script center', 'complianz-gdpr' ) );
?>
<?php
COMPLIANZ::$field->get_fields( 'custom-scripts' );
