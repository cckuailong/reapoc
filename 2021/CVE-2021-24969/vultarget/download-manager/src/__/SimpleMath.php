<?php


namespace WPDM\__;


class SimpleMath
{

    static function show(){
        $img = imagecreate(100, 50);

        $textbgcolor = imagecolorallocate($img, 173, 230, 181);
        $textcolor = imagecolorallocate($img, 0, 192, 255);
        $ops = array('+', '*', '-');
        $op = $ops[random_int(0, 2)];
        $num1 = random_int(1, 9);
        $num2 = random_int(1, 9);
        self::saveResult($num1, $num2, $op);
        $txt = "$num1 $op $num2";
        imagestring($img, 5, 5, 5, $txt, $textcolor);
        ob_start();
        imagepng($img);
        printf('<div class="w3eden"><div class="input-group"><div class="input-group-prepend"><div class="input-group-text"><img src="data:image/png;base64,%s"/ width="100"></div></div><input type="text" class="form-control"></div></div>', base64_encode(ob_get_clean()));

    }

    private static function saveResult($num1, $num2, $op){
        $result = 0;
        if($op == '-') $result = $num1 - $num2;
        if($op == '+') $result = $num1 + $num2;
        if($op == '*') $result = $num1 * $num2;
        Session::set('__wpdm_simplemath_result', $result);
    }

}
