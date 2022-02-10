jQuery(document).ready(function($){

if ($("#pagecredits").length) {
  $.getJSON(ExtData.Credit, function(data) {
   $.each(data.contacts, function(keyp, valp) {
     var addressr="<tr>\n<td class='cell-l'>" + valp.name + "</td>\n<td class='cell-r'>" + valp.properties + "</td>\n</tr>\n";
     $(addressr).appendTo("#addresses");

   });
  });
  $.getJSON(ExtData.Lang, function(data) {
   $.each(data.translation, function(keyp, valp) {
     var addressr="<tr>"+
                  "<td class='cell-l'>" +
                  "<img style='border:0px;height:16px;' alt='" + valp.domain + "' title='"+valp.domain+"'" + "src='"+ExtData.Domain+"/"+ valp.domain + ".png' /> " +
                  valp.lang + "</td>\n<td class='cell-r'>" + valp.properties + "</td>" +
                  "<td class='cell-r'>" + valp.status + "</td></tr>\n";
     $(addressr).appendTo("#langr");

   });
  });
  $.getJSON(ExtData.Resources, function(data) {
   $.each(data.ressources, function(keyp, valp) {
     var row="<tr>"+
             "<td class='cell-l'>" + valp.ref + "</td>\n" +
             "<td class='cell-r'>" + valp.description + "</td>\n" +
             "<td class='cell-r'><a href=\"" + valp.website + "\">" + valp.website + "</a></td>\n" +
             "</tr>\n";
     $(row).appendTo("#ressourceslist");
   });
  });
  $.getJSON(ExtData.Donation, function(data) {
   $.each(data.donation, function(keyp, valp) {
     var row="<tr>\n<td class='cell-l'>" + valp.donator + "</td>\n<td class='cell-r'>" + valp.date + "</td>\n</tr>\n";
     $(row).appendTo("#donatorlist");

   });
  });
}



  // Options Page > Mail Notification tab
  setTimeout(function() {
     $('#mailsent').fadeOut();
     $('#optionsupdated').fadeOut();

   }, 4000);

   $( "#close" ).click(function() {
     $('#nspnotice').fadeOut();
   });

   // --- picker date
   var pickerfrom = new Pikaday({
           field: document.getElementById('datefrom'),
           format: 'YYYYMMDD',
           onSelect: function() {
   							pickerfrom.gotoToday();
           },
   				onClose: function() {
   					//$( "#date_report" ).text( this.getMoment().format('DD/MM/YYYY') );
             datefilename=this.getMoment().format('YYYYMMDD');
 				  }
   });

   var pickerto = new Pikaday({
           field: document.getElementById('dateto'),
           format: 'YYYYMMDD',
           onSelect: function() {
                 pickerto.gotoToday();
           },
           onClose: function() {
             //$( "#date_report" ).text( this.getMoment().format('DD/MM/YYYY') );
             //datefilename=this.getMoment().format('YYYYMMDD');
           }
   });


   // Toogle spider in overview agent
   $('#hider').click(function () {
       if ($('#hider').text()===("Hide Spiders")) {
         $('#hider').text("Show Spiders");
         $('.spiderhide').hide();
       }
       else {
         $('#hider').text("Hide Spiders");
         $('.spiderhide').show();
       }
   });



  //  $('#myoptions').get(0).reset();

  // $("#myform input[type='radio']:checked").val();
  // $('#dis').on('change', function() {
  //   var set;
    if($('#dis:checked').val()==='disabled') {
      $("#mail_freq").attr("disabled", true);
      $("#mail_time").attr("disabled", true);
      $("#mail_address").attr("disabled", true);
      $("#testmail").attr("disabled", true);
      $("#sender").attr("disabled", true);

    }

    if($('#ena:checked').val()==='enabled') {
      $("#mail_freq").attr("disabled", false);
      $("#mail_time").attr("disabled", false);
      $("#mail_address").attr("disabled", false);
      $("#testmail").attr("disabled", false);
      $("#sender").attr("disabled", false);

    }
    //   if($('#ena:checked').val()==='enabled')
    //
    //   set=false;
    // $("#mail_freq").attr("disabled", set);
    // $("#mail_time").attr("disabled", set);
    // $("#mail_address").attr("disabled", set);
  //  alert($('#dis:checked').val());
  //  alert($('#dis:checked').attr());

  // alert("toto");
// });

  $( "#dis" ).click(function() {
    $("#mail_freq").attr("disabled", true);
    $("#mail_time").attr("disabled", true);
    $("#mail_address").attr("disabled", true);
    $("#testmail").attr("disabled", true);
    $("#sender").attr("disabled", true);
  });
  $( "#ena" ).click(function() {
    $("#mail_freq").attr("disabled", false);
    $("#mail_time").attr("disabled", false);
    $("#mail_address").attr("disabled", false);
    $("#testmail").attr("disabled", false);
    $("#sender").attr("disabled", false);
  });





});


function validateCode() {
  // var TCode = document.getElementById('TCode').value;
  var obj = document.getElementById("newstatpress_apikey").value;

  if( /[^a-zA-Z0-9]/.test( obj ) ) {
     alert('Input is not alphanumeric');
     return false;
  }
  return true;
}

function randomString(length, chars) {
   var result = '';
   for (var i = length; i > 0; --i) result += chars[Math.round(Math.random() * (chars.length - 1))];
   return result;
}

function nspGenerateAPIKey() {
   var obj = document.getElementById("newstatpress_apikey");
   var txt = randomString(128, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
   obj.value = txt;
}
