(function($) {
  $.fn.conditionize = function(options) {  
    
    var settings = $.extend({
        hideJS: true
    }, options );
    
    $.fn.rmAnd= function(obj){
      for(var i = 0, len = obj.length - 1; i < len && obj[i]; i++);
      return obj[i];
    }

    $.fn.rmOr= function(obj) {
      for(var i = 0, len = obj.length - 1; i < len && !obj[i]; i++);
      return obj[i];
    };

    // If array is empty, undefined is returned.  If not empty, the first element
    // that evaluates to false is returned.  If no elements evaluate to false, the
    // last element in the array is returned.
    /*Array.prototype.rm_and = function() {
      for(var i = 0, len = this.length - 1; i < len && this[i]; i++);
      return this[i];
    };*/

    // If array is empty, undefined is returned.  If not empty, the first element
    // that evaluates to true is returned.  If no elements evaluate to true, the
    // last element in the array is returned.
   /* Array.prototype.rm_or = function() {
      for(var i = 0, len = this.length - 1; i < len && !this[i]; i++);
      return this[i];
    };*/
    
    $.fn.eval = function(valueIs, valueShould, operator,multiValue) {
      multiValue= multiValue || false;
       
      switch(operator) {
        case '==':
             if(multiValue){
                if(isNaN(valueIs) && valueIs){
                    valueIs= valueIs.split(",");
                }
            }
            if(valueIs instanceof Array){
                for(i=0;i<valueIs.length;i++){
                    if(valueIs[i]==valueShould){
                       return true;
                    }
                }
                return false;
            }
            else
              return valueIs == valueShould;
        case '!=': console.log(valueIs, operator,valueShould,multiValue);
             if(multiValue){
                if(isNaN(valueIs) && valueIs){
                    valueIs= valueIs.split(",");
                }
            }
            if(valueIs instanceof Array){
                for(i=0;i<valueIs.length;i++){
                    if(!(valueIs[i]!=valueShould)){
                       return false;
                    }
                }
                return true;
            }
            else
            return valueIs != valueShould;
        case '<=':
            if(multiValue){
                if(isNaN(valueIs) && valueIs){
                    valueIs= valueIs.split(",");
                }
            }
            if(valueIs instanceof Array){
                for(i=0;i<valueIs.length;i++){
                    if(!(valueIs[i]<=valueShould)){
                       return false;
                    }
                }
                return true;
            }
            else{
                if(valueIs=="" && !isNaN(valueShould)){
                    return false;
                }
                return valueIs <= valueShould;
            }
        case '<':
            if(multiValue){
                if(isNaN(valueIs) && valueIs){
                    valueIs= valueIs.split(",");
                }
            }
            if(valueIs instanceof Array){
                for(i=0;i<valueIs.length;i++){
                    if(!(valueIs[i]<valueShould)){
                       return false;
                    }
                }
                return true;
            }
            else{
                if(valueIs=="" && !isNaN(valueShould)){
                    return false;
                }
                return valueIs < valueShould;
            }
        case '>=':
            if(multiValue){
                if(isNaN(valueIs) && valueIs){
                    valueIs= valueIs.split(",");
                }
            }
            if(valueIs instanceof Array){
                for(i=0;i<valueIs.length;i++){
                    if(!(valueIs[i]>=valueShould)){
                       return false;
                    }
                }
                return true;
            }
            else{
                if(valueIs=="" && !isNaN(valueShould)){
                    return false;
                }
                return valueIs >= valueShould;
            }
            
        case '>':
            if(multiValue){
                if(isNaN(valueIs) && valueIs){
                    valueIs= valueIs.split(",");
                }
            }
            if(valueIs instanceof Array){
                for(i=0;i<valueIs.length;i++){
                    if(!(valueIs[i]>valueShould)){
                       return false;
                    }
                }
                return true;
            }
            else{
                if(valueIs=="" && !isNaN(valueShould)){
                    return false;
                }
                return valueIs > valueShould;
            }
            
        case 'in':{
                if(multiValue){
                    if(isNaN(valueIs) && valueIs){
                        valueIs= valueIs.split(",");
                    }                    
                    if(isNaN(valueShould)){
                        valueShould= valueShould.split(",");
                    } 
                }
                
                if(valueIs instanceof Array && valueShould instanceof Array){
                    commonValues= valueIs.filter(function(n) {
                        return valueShould.indexOf(n) != -1;
                    });
                    if(commonValues.length>0)
                        return true;
                    else
                        return false;
                } else if(valueIs instanceof Array){
                    valueShould= valueShould.toString();
                    return valueIs.indexOf(valueShould) != -1;
                } else if(valueShould instanceof Array){
                    valueIs= valueIs.toString();
                    return valueShould.indexOf(valueIs) != -1;
                }
                else{
                    return valueIs.indexOf(valueShould)!==-1; 
                }
                 
        }
        case '_blank':
            return valueIs=="";
        case '_not_blank':
            return valueIs!="";    
      }
      
    }
    
    $.fn.isNumber= function(obj){return (/number/).test(typeof obj);}
    
    
    $.fn.getValues= function(obj){
        if($.fn.isNumber(obj))
            return [obj];
        else
        {
            return obj.split("|").map(function(x){ return x; });
        }
    }
    
    $.fn.showOrHide = function(listenTo, listenFor, operator,combinator, $section,subject,onLoad) {
      var listenForValues=  $.fn.getValues(listenFor);
      var operators= operator.split('|'),resultStatus=[],to,pass= false;
      var to,type,valueIs,valueShould;
      var skip= false;
      var results= []; //console.log(listenForValues);
      for(var i=0;i<listenForValues.length;i++){ 
        to = "[name=" + listenTo[i] + "]";
      
        type= listenTo[i].split("_")[0];
        valueIs= $(to).val();
        valueShould= listenForValues[i];
       
        if(type=='jQueryUIDate' || type=='Bdate')
        {  
            if(operators[i]=='_blank' && valueIs==""){
                    results.push(true);
                    continue;
            }
              
            if($(to).datepicker( "getDate" )!=null){ 
                valueIs= $(to).datepicker( "getDate" ).getTime();
                if(operators[i]=='_not_blank' && listenTo[i]=="_"){
                    results.push(false);
                    continue;
                }
                else{
                    valueShould= listenForValues[i];
                    var dateFormat= $(to).datepicker('option', 'dateFormat');
                    if(valueShould!="_")
                    valueShould= $.datepicker.parseDate(dateFormat,valueShould).getTime(); 
                } 
            }
            else{
                results.push(false);
               continue;
            } 
        } else if(["<=",">=","<",">"].indexOf(operators[i])>=0)
        {   
            if(onLoad && ["<=","<"].indexOf(operators[i]>=0)) 
                skip= true;
            valueIs= parseFloat(valueIs);
            valueShould= parseFloat(valueShould);
        }else{
            valueIs= $(to).val()!==undefined && $(to).val()!==null ? $(to).val().toString().toLowerCase() : $(to).val();
            valueShould= $.fn.isNumber(listenForValues[i])? listenForValues[i]: listenForValues[i].toLowerCase();
        }
        
        if($(to).is('input[type=text],input[type=url],input[type=number],input[type=password],input[type=email],textarea') && $.fn.eval(valueIs, valueShould, operators[i]) && !$(to).is(':radio')){
            results.push(true);
        }
        else if($(to).is('select') && $.fn.eval(valueIs, valueShould, operators[i],true)){
            results.push(true);
        }
        else if (($(to).is(':radio') || $(to).is(':checkbox')) && !$(to).is(':checked') && $.fn.eval('', valueShould, operators[i],true)){
             results.push(true);
        }
        else if($(to + ":checked").filter(function(idx, elem)
             { values= [];
                $(to + ":checked").each(function(){
                    values.push($(this).val());
                }); 
               return $.fn.eval(values.toString().toLowerCase(), valueShould, operators[i],true);
             }).length > 0 )
            results.push(true);
        else 
           results.push(false);
      }  
     var pass= combinator=='AND' ? $.fn.rmAnd(results) : $.fn.rmOr(results);
         
       
     if (pass && !skip) {
        $section.removeClass('ignore');
        $section.closest('.rmrow').slideDown();
         $section.prop('disabled',false);
        $section.closest('.rmrow :input').each(function(){
            if($(this).attr('initial-state')){
                $(this).attr('required','required');
            }
        });
        $.fn.updateConditionalFieldsIds(subject.attr('name'),1);
        $section.closest('.rmrow :input').each(function(){
            if($(this).prop('initial-state')){
                $(this).attr('required');
            }
        });
     }
     else {
        $section.addClass('ignore');
        //$section.val('');
        $section.closest('.rmrow :input').each(function(){
            if($(this).attr('initial-state')){
                $(this).removeAttr('required');
            }
        });
        $section.closest('.rmrow').slideUp();
         $section.prop('disabled',true);
        $.fn.updateConditionalFieldsIds(subject.attr('name'),0);
     }
    }
    
     // Add hidden field names for server side tracking
    $.fn.updateConditionalFieldsIds= function(fieldName,add)
    { 
        var fieldsArr=[];
        var currentFields= $("#rm_cond_hidden_fields");
        if(currentFields!="")
        fieldsArr= currentFields.val().split(",");
        var pos= $.inArray(fieldName,fieldsArr);
        
        if(add==1 && pos>=0)
                fieldsArr.splice(pos, 1); 
        else if(pos==-1)
                fieldsArr.push(fieldName);
       
        fieldsArr.length==0 ? currentFields.val(""): currentFields.val(fieldsArr.join());
    }
    
    return this.each( function() {
       var cleanSelectors= $(this).data('cond-option').toString().replace(/(:|\.|\[|\]|,)/g, "\\$1").split("|");
        for(var i=0;i<cleanSelectors.length;i++){
        var cleanSelector = cleanSelectors[i]; 
        var listenTo = (cleanSelector.substring(0,1)=='|'?cleanSelector:"[name=" + cleanSelector + "]");
        var listenFor = $(this).data('cond-value');
        var operator = $(this).data('cond-operator') ? $(this).data('cond-operator') : '==';
        var combinator = $(this).data('cond-comb');
   
        var $section = $(this);
        var subject= $(this);
        //Set up event listener
        
        $(listenTo).on('change', function() { 
          $.fn.showOrHide(cleanSelectors, listenFor, operator,combinator, $section,subject,false);
        }); 
        //If setting was chosen, hide everything first...
        if (settings.hideJS) {
          $(this).closest('.rmrow').hide();
          $(this).addClass('ignore');
        //  $.fn.updateConditionalFieldsIds(subject.attr('name'),0);

        }
      //Show based on current value on page load
      
      $.fn.showOrHide(cleanSelectors, listenFor, operator,combinator, $section,subject,true);
      }    
              
    });
  }
}(jQuery));



/* Intializing the necessary scripts*/
jQuery(document).ready(function(){
    $=jQuery;
    if($(".data-conditional").length>0){
        $('.rmrow :input').each(function(){
            if($(this).prop('required')){
                $(this).attr('initial-state','required');
            }
        });
        $(".data-conditional").conditionize({});
    }
    

    
});