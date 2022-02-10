<?php

require('php-captcha.inc.php');

$fonts = array('', 'Bd', 'BI', 'It', 'MoBd', 'MoBI', 'MoIt', 'Mono', 'Se', 'SeBd');
for($i = 0; $i < count($fonts); $i++ )
	$fonts[$i] = 'ttf-bitstream-vera-1.10/Vera'.$fonts[$i].'.ttf';

$alphabet = 'a_b_c_d_e_f_g_h_i_j_k_l_m_n_o_p_q_r_s_t_u_v_w_x_y_z';
$alphabet = explode('_', $alphabet);
shuffle($alphabet);

$captchaText = '';
for($i = 0; $i < 5; $i++ )
{
	$captchaText .= $alphabet[$i];
}

$time = time();

setcookie('Captcha', md5("ORHFUKELFPTUEODKFJ".$captchaText.$_SERVER['REMOTE_ADDR'].$time).'.'.$time, null, '/');

$oVisualCaptcha = new PhpCaptcha($fonts, strlen($captchaText) * 23, 60);
$oVisualCaptcha->UseColour(true);
$oVisualCaptcha->Create($captchaText);

?>