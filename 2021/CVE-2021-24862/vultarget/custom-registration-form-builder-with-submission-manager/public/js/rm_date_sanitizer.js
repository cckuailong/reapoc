
function rm_sanitize_date_selector( form_id )
{
    var year = jQuery('#id_rm_fac_year_'+form_id).val();
    var month = jQuery('#id_rm_fac_month_'+form_id).val();
    var days_in_month = [31,28,31,30,31,30,31,31,30,31,30,31];

    if(year != '')
    {
        var isLeapYear = year%100?!(year%4):!(year%400);
        if(isLeapYear)
            days_in_month[1] = 29;
        else
            days_in_month[1] = 28;
    }

    var options = jQuery('#id_rm_fac_day_'+form_id+' option');           

    jQuery("#id_rm_fac_day_"+form_id+" option").each(function()
    {
        var opt_val = jQuery(this).val();

        /* Add $(this).val() to your list*/
        if(opt_val > days_in_month[month-1])
            jQuery("#id_rm_fac_day_"+form_id+" option[value='"+opt_val+"']").remove();
    });

    if(options.length < days_in_month[month-1])
    {
        var from = options.length+1;
        var to = days_in_month[month-1];
        for(var i=from;i<=to;i++)
            jQuery("#id_rm_fac_day_"+form_id).append('<option value="'+i+'">'+i+'</option>');
    }


}