<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php echo $this->get_title(); ?></title>
	<style type="text/css"><?php $this->template_styles(); ?></style>
	<style type="text/css"><?php do_action( 'wpo_wcpdf_custom_styles', $this->get_type(), $this ); ?></style>
</head>
<body class="<?php echo apply_filters( 'wpo_wcpdf_body_class', $this->get_type(), $this ); ?>">
<?php echo $content; ?>
</body>
</html>