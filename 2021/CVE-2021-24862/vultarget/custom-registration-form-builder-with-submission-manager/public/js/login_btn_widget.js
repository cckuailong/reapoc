function rmw_login_method_change(obj){
    $= jQuery;
    $(obj).closest('form').find('[type=submit]').trigger('click');
}