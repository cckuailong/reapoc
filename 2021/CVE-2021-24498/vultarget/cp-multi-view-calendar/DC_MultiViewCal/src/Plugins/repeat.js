function fluidDialog() {
        var $visible = $(".ui-dialog:visible");
        // each open dialog
        $visible.each(function () {
            var $this = $(this);
            var dialog = $this.find(".ui-dialog-content");
            if (dialog.dialog("option","fluid")) {
                var wWidth = $(window).width();
                // check window width against dialog width
                if (wWidth < (parseInt(dialog.dialog("option","maxWidth")) + 50))  {
                    // keep dialog from filling entire screen
                    $this.css("max-width", "90%");
                } else {
                    // fix maxWidth bug
                    $this.css("max-width", dialog.dialog("option","maxWidth") + "px");
                }
                //reposition dialog
                dialog.dialog("option","position", dialog.dialog("option","position"));
            }
        });
    
}
$(window).resize(function () {
   fluidDialog();
});
$(document).on("dialogopen", ".ui-dialog", function (event, ui) {
    fluidDialog();
});
$(function() {
      var weekDays = new Array("SU","MO","TU","WE","TH","FR","SA");
      var weekDaysLarge = new Array(i18n.dcmvcal.dateformat.sunday, i18n.dcmvcal.dateformat.monday, i18n.dcmvcal.dateformat.tuesday, i18n.dcmvcal.dateformat.wednesday, i18n.dcmvcal.dateformat.thursday, i18n.dcmvcal.dateformat.friday, i18n.dcmvcal.dateformat.saturday);
      var monthsName = new Array(i18n.dcmvcal.dateformat.jan, i18n.dcmvcal.dateformat.feb, i18n.dcmvcal.dateformat.mar, i18n.dcmvcal.dateformat.apr, i18n.dcmvcal.dateformat.may, i18n.dcmvcal.dateformat.jun, i18n.dcmvcal.dateformat.jul, i18n.dcmvcal.dateformat.aug, i18n.dcmvcal.dateformat.sep, i18n.dcmvcal.dateformat.oct, i18n.dcmvcal.dateformat.nov, i18n.dcmvcal.dateformat.dec);
      var prefixes = new Array(i18n.dcmvcal.first, i18n.dcmvcal.second, i18n.dcmvcal.third, i18n.dcmvcal.fourth, i18n.dcmvcal.last);
      
      openRepeatWin = function(){
          loadRepeatData($("#rrule").val());
          $("#repeat").dialog({modal: true,resizable: false,maxWidth: 420,fluid: true,open: function(event, ui){fluidDialog();},width:420}).parent().addClass("mv_dlg").addClass("mv_dlg_editevent").addClass("infocontainer") ;
          $(".mv_dlg").css("height","0px");
      }
      $("#savebtnRepeat,#closebtnRepeat").button();
      $("#savebtnRepeat" ).button( "option", "label", i18n.dcmvcal.i_save );
      $("#closebtnRepeat" ).button( "option", "label", i18n.dcmvcal.i_close );     
      
      
      $("#rsh2").html(i18n.dcmvcal.edit_recurring_event);
      $("#rsp1").html(i18n.dcmvcal.would_you_like_to_change_only_this_event_all_events_in_the_series_or_this_and_all_following_events_in_the_series);
      $("#r_save_one").html(i18n.dcmvcal.only_this_event);
      $("#rss1").html(i18n.dcmvcal.all_other_events_in_the_series_will_remain_the_same);
      $("#r_save_following").html(i18n.dcmvcal.following_events);
      $("#rss2").html(i18n.dcmvcal.this_and_all_the_following_events_will_be_changed);
      $("#rss3").html(i18n.dcmvcal.any_changes_to_future_events_will_be_lost);
      $("#r_save_all").html(i18n.dcmvcal.all_events);
      $("#rss4").html(i18n.dcmvcal.all_events_in_the_series_will_be_changed);
      $("#rss5").html(i18n.dcmvcal.any_changes_made_to_other_events_will_be_kept);
      $("#r_save_cancel").html(i18n.dcmvcal.cancel_this_change);
      $("#rdh2").html(i18n.dcmvcal.delete_recurring_event);
      $("#rdp1").html(i18n.dcmvcal.would_you_like_to_delete_only_this_event_all_events_in_the_series_or_this_and_all_future_events_in_the_series);
      $("#r_delete_one").html(i18n.dcmvcal.only_this_instance);
      $("#rds1").html(i18n.dcmvcal.all_other_events_in_the_series_will_remain);
      $("#r_delete_following").html(i18n.dcmvcal.all_following);
      $("#rds2").html(i18n.dcmvcal.this_and_all_the_following_events_will_be_deleted);
      $("#r_delete_all").html(i18n.dcmvcal.all_events_in_the_series);
      $("#rds3").html(i18n.dcmvcal.all_events_in_the_series_will_be_deleted);
      $("#r_delete_cancel").html(i18n.dcmvcal.cancel_this_change);
      
      $("#rl1").html(i18n.dcmvcal.repeats);
      $("#opt0").html(i18n.dcmvcal.daily);
      $("#opt1").html(i18n.dcmvcal.every_weekday_monday_to_friday);
      $("#opt2").html(i18n.dcmvcal.every_monday_wednesday_and_friday);
      $("#opt3").html(i18n.dcmvcal.every_tuesday_and_thursday);
      $("#opt4").html(i18n.dcmvcal.weekly);
      $("#opt5").html(i18n.dcmvcal.monthly);
      $("#opt6").html(i18n.dcmvcal.yearly);
      $("#rl2").html(i18n.dcmvcal.repeat_every);
      $("#interval_label").html(i18n.dcmvcal.weeks);
      $("#rl3").html(i18n.dcmvcal.repeat_on);
      $("#chk0").html(i18n.dcmvcal.dateformat.sun2.toUpperCase());
      $("#chk1").html(i18n.dcmvcal.dateformat.mon2.toUpperCase());
      $("#chk2").html(i18n.dcmvcal.dateformat.tue2.toUpperCase());
      $("#chk3").html(i18n.dcmvcal.dateformat.wed2.toUpperCase());
      $("#chk4").html(i18n.dcmvcal.dateformat.thu2.toUpperCase());
      $("#chk5").html(i18n.dcmvcal.dateformat.fri2.toUpperCase());
      $("#chk6").html(i18n.dcmvcal.dateformat.sat2.toUpperCase());
      $("#rl4").html(i18n.dcmvcal.repeat_by);
      $("#bydaymonth1").html(i18n.dcmvcal.day_of_the_month);
      $("#bydaymonth2").html(i18n.dcmvcal.day_of_the_week);
      $("#rl5").html(i18n.dcmvcal.starts_on);
      $("#rl6").html(i18n.dcmvcal.ends);
      $("#end1").html(i18n.dcmvcal.never);
      $("#end21").html(i18n.dcmvcal.after);
      $("#end22").html(i18n.dcmvcal.occurrences);
      $("#end3").html(i18n.dcmvcal.on);
      $("#rl7").html(i18n.dcmvcal.summary);
      
      
      
      $("#closebtnRepeat").click(function() {
          if ($("#rrule").val()=="")
          {
              $("#format").html("");
              $("#repeatspan").html("");
              $("#repeatcheckbox").attr("checked","");
          }
          $("#repeat").dialog('close');
      });
      $("#savebtnRepeat").click(function() {
          $("#rrule").val($("#format").val());
          if ($("#format").val()=="")
          {
              $("#repeatspan").html("");
              $("#repeatcheckbox").attr("checked","");
          }    
          else
          {
              //$("#repeatspan").html(summary);
              $("#repeatcheckbox").attr("checked","checked");
          }
          $("#repeat").dialog('close'); 
      });
      if (!DateAdd || typeof (DateDiff) != "function") {
          var DateAdd = function(interval, number, idate) {
              number = parseInt(number);
              var date;
              if (typeof (idate) == "string") {
                  date = idate.split(/\D/);
                  eval("var date = new Date(" + date.join(",") + ")");
              }
      
              if (typeof (idate) == "object") {
                  date = new Date(idate.toString());
              }
              switch (interval) {
                  case "y": date.setFullYear(date.getFullYear() + number); break;
                  case "m": date.setMonth(date.getMonth() + number); break;
                  case "d": date.setDate(date.getDate() + number); break;
                  case "w": date.setDate(date.getDate() + 7 * number); break;
                  case "h": date.setHours(date.getHours() + number); break;
                  case "n": date.setMinutes(date.getMinutes() + number); break;
                  case "s": date.setSeconds(date.getSeconds() + number); break;
                  case "l": date.setMilliseconds(date.getMilliseconds() + number); break;
              }
              return date;
          }
      }
      function weekAndDay(date) {
            return (0 | (date.getDate()-1) / 7);
      }
      timeToUntilString= function(time) {
          var date = new Date(time);
          var comp, comps = [
              date.getUTCFullYear(),
              date.getUTCMonth() + 1,
              date.getUTCDate(),
              'T',
              date.getUTCHours(),
              date.getUTCMinutes(),
              date.getUTCSeconds(),
              'Z'
          ];
          for (var i = 0; i < comps.length; i++) {
              comp = comps[i];
              if (!/[TZ]/.test(comp) && comp < 10) {
                  comps[i] = '0' + String(comp);
              }
          }
          return comps.join('');
      }
      loadRepeatData = function(data)
      {
          for (var i=1;i<=30;i++)
              $("#interval").append('<option value="'+i+'">'+i+'</option>');
          for (var i=1;i<100;i++)
              $("#end_after").append('<option value="'+i+'">'+i+'</option>');
          $("#end_after").val(10);
          var d = $("#starts").html().split("/");
          var arrs = $("#starts").html().split(i18n.dcmvcal.dateformat.separator);
          var year = arrs[i18n.dcmvcal.dateformat.year_index];
          var month = arrs[i18n.dcmvcal.dateformat.month_index];
          var day = arrs[i18n.dcmvcal.dateformat.day_index];
          $("#stpartdatelast").val([month,day,year].join("/"));
          var currentDate = new Date(year, month-1, day);
          $("#end_until_input").val(d[0]+"/"+d[1]+"/"+(parseInt(d[2])+1))
          if (data == "")
              data = "FREQ=WEEKLY;BYDAY="+weekDays[currentDate.getDay()]+"";
          var v_freq = 4;
          var d = data.split(";");
          for (var i=0;i<d.length;i++)
          {
              var dd = d[i].split("=");
              d[i] = {k:dd[0],v:dd[1]};
          }
          for (var i=0;i<d.length;i++)
          {
              switch(d[i].k)
              {
                  case "FREQ":
                        switch(d[i].v)
                        {
                            case "DAILY":
                                v_freq = 0;
                            break;
                            case "WEEKLY":
                                v_freq = 4;
                            break;
                            case "MONTHLY":
                                v_freq = 5;
                            break;
                            case "YEARLY":
                                v_freq = 6;
                            break;
                        }
                  break;
                  case "INTERVAL":
                      $("#interval").val(d[i].v);
                  break;
                  case "BYDAY":

                        var dd = d[i].v.split(",");

                        var sample1 = ["MO","TU","WE","TH","FR"]; //Every weekday (Monday to Friday) // ["MO","TU","WE","TH","FR"];
                        if ($(dd).not(sample1).length == 0 && $(sample1).not(dd).length == 0)
                            v_freq = 1;

                        var sample2 = ["MO","WE","FR"]; //Every Monday, Wednesday, and Friday // ["MO","WE","FR"];
                        if ($(dd).not(sample2).length == 0 && $(sample2).not(dd).length == 0)
                            v_freq = 2;

                        var sample3 = ["TU","TH"]; //Every Tuesday, and Thursday // ["TU","TH"];
                        if ($(dd).not(sample3).length == 0 && $(sample3).not(dd).length == 0)
                            v_freq = 3;
                        for (j = 0; j < dd.length; j++) {
                            day = dd[j];
                            if (day.length == 2) { // MO, TU, ... instanceof Weekday
                                $("#byday"+dd[j]).attr("checked","checked");
                            } else { // -1MO, +3FR, 1SO, ... instanceof MONTHLY, YEARLY
                                day = day.match(/^([+-]?\d)([A-Z]{2})$/);
                                n = Number(day[1]);
                                wday = day[2];
                                $("#byday_w").attr("checked","checked");
                            }
                        }
                        for (var j=0;j<dd.length;j++)
                            $("#byday"+dd[j]).attr("checked","checked");
                    break;
                  case "COUNT":
                      $("#end_count").attr("checked","checked");
                      $("#end_after").val(d[i].v);
                  break;
                  case "UNTIL":
                      var day = /(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})Z/.exec(d[i].v);  
                      var until = new Date(Date.UTC(day[1], day[2] - 1,day[3], day[4], day[5], day[6]));
                      $("#end_until").attr("checked","checked");
                      $("#end_until_input").val((until.getMonth()+1)+"/"+until.getDate()+"/"+until.getFullYear());
                  break;
                  case "BYMONTHDAY":
                  case "BYMONTH":
                      $("#byday_m").attr("checked","checked");
                  break;
                  
                  

              }
          }
          summaryDisplay = function()
          {
              var v = parseInt($("#freq").val());
              var summary = "";
              var format = "";
              switch(v)
              {
                  case 0:
                      format += "FREQ=DAILY";
                      if ($("#interval").val()==1)
                          summary += i18n.dcmvcal.daily;
                      else
                      {
                          summary += i18n.dcmvcal.every+" "+$("#interval").val()+" "+i18n.dcmvcal.day_plural;
                          format += ";INTERVAL="+$("#interval").val();
                      }

                  break;
                  case 1:
                      format += "FREQ=WEEKLY;BYDAY=MO,TU,WE,TH,FR";
                      summary += i18n.dcmvcal.weekly_on_weekdays;
                  break;
                  case 2:
                      format += "FREQ=WEEKLY;BYDAY=MO,WE,FR";
                      summary += i18n.dcmvcal.weekly_on_monday_wednesday_friday;
                  break;
                  case 3:
                      format += "FREQ=WEEKLY;BYDAY=TU,TH";
                      summary += i18n.dcmvcal.weekly_on_tuesday_thursday;
                  break;
                  case 4:
                      format += "FREQ=WEEKLY";
                      for (var i=0;i<weekDays.length;i++)
                      {                          
                          if ($("#byday"+weekDays[i]).is(":checked"))
                          {
                              if (summary =="")
                              {
                                  summary += " "+i18n.dcmvcal.on+" ";
                                  format += ";BYDAY=";
                              }
                              else
                              {
                                  summary += ", ";
                                  format += ",";
                              }
                              summary += weekDaysLarge[i];
                              format += weekDays[i];
                          }
                      }    
                      if ($("#interval").val()==1)
                          summary = i18n.dcmvcal.weekly+summary;
                      else
                      {
                          summary = i18n.dcmvcal.every+" "+$("#interval").val()+" "+i18n.dcmvcal.weeks+summary;
                          format += ";INTERVAL="+$("#interval").val();
                      }
                  break;
                  case 5:
                      format += "FREQ=MONTHLY";
                      if ($("#byday_m").is(":checked"))
                      {
                          summary += " "+i18n.dcmvcal.on_day+" "+currentDate.getDate();
                          format += ";BYMONTHDAY="+currentDate.getDate();
                      }
                      else
                      {
                          summary += " "+i18n.dcmvcal.on_the+" "+prefixes[weekAndDay(currentDate)]+ " " +weekDaysLarge[currentDate.getDay()];
                          format += ";BYDAY="+(weekAndDay(currentDate)==4?-1:(weekAndDay(currentDate)+1))+weekDays[currentDate.getDay()];
                      }
                      if ($("#interval").val()==1)
                          summary = i18n.dcmvcal.monthly+summary;
                      else
                      {
                          summary = i18n.dcmvcal.every+" "+$("#interval").val()+" "+i18n.dcmvcal.months+summary;
                          format += ";INTERVAL="+$("#interval").val();
                      }
                  break;
                  case 6:
                      format += "FREQ=YEARLY;BYMONTH="+(currentDate.getMonth()+1);
                      if ($("#byday_m").is(":checked"))
                      {
                          summary += " "+i18n.dcmvcal.on+" " + monthsName[currentDate.getMonth()] + " " + currentDate.getDate();
                      }
                      else
                      {
                          summary += " "+i18n.dcmvcal.on+" " + monthsName[currentDate.getMonth()] + ", "+prefixes[weekAndDay(currentDate)]+ " " +weekDaysLarge[currentDate.getDay()];
                          format += ";BYDAY="+(weekAndDay(currentDate)+1)+weekDays[currentDate.getDay()];
                      }
                      if ($("#interval").val()==1)
                          summary = i18n.dcmvcal.annually+summary;
                      else
                      {
                          summary = i18n.dcmvcal.every+" "+$("#interval").val()+" "+i18n.dcmvcal.years+summary;
                          format += ";INTERVAL="+$("#interval").val();
                      }
                  break;
              }
              if ($("#end_count").is(":checked"))
              {
                  if (parseInt($("#end_after").val())==1)
                      summary = i18n.dcmvcal.once;
                  else
                  {
                      summary += ", "+$("#end_after").val()+" "+i18n.dcmvcal.times;
                      format += ";COUNT="+$("#end_after").val();
                  }
              }
              else if ($("#end_until").is(":checked"))
              {
                  if ($("#end_until_input").val()!="")
                  {
                      var d = $("#end_until_input").val().split("/");
                      var endDate = new Date(d[2], d[0]-1, d[1],23,59,59);
                      summary += ", "+i18n.dcmvcal.until+" " + monthsName[d[0]-1] + " " + d[1] + ", " + d[2];
                      format += ";UNTIL="+timeToUntilString(endDate);
                  }
              }
              $("#summary").html(summary);
              $("#format").val(format);
              if ($("#format").val()=="")
              {
                  $("#repeatspan").html("");
                  $("#repeatcheckbox").attr("checked","");
              }    
              else
              {
                  $("#repeatspan").html(summary);
                  $("#repeatcheckbox").attr("checked","checked");
              }    
              
              
          }
          changeDisplay = function(v)
          {
              if (v==1 || v==2 || v==3)
                  $("#intervaldiv").css("display","none");
              else
              {
                  $("#intervaldiv").css("display","block");
                  if (v==0)  $("#interval_label").html(i18n.dcmvcal.day_plural);
                  else if (v==4)  $("#interval_label").html(i18n.dcmvcal.weeks);
                  else if (v==5)  $("#interval_label").html(i18n.dcmvcal.months);
                  else if (v==6)  $("#interval_label").html(i18n.dcmvcal.years);
              }
              if (v==4)
                  $("#bydayweek").css("display","block");
              else
                  $("#bydayweek").css("display","none");  //none
              if (v==5 || v==6)
                  $("#bydaymonth").css("display","block");
              else
                  $("#bydaymonth").css("display","none");
              summaryDisplay();

          }


          $("#freq").val(v_freq);
          changeDisplay(v_freq);

          $("#freq").change(function(){
              changeDisplay($(this).val());
          });
          $("#interval").change(function(){
              summaryDisplay();
          });
          $("#end_never").change(function(){
              summaryDisplay();
          });
          $("#end_count").change(function(){
              summaryDisplay();
          });
          $("#end_until").change(function(){
              summaryDisplay();
          });
          $("#end_after").change(function(){
              summaryDisplay();
          });
          $("#end_until_input").change(function(){
              summaryDisplay();
          });
          $(".bydayw").change(function(){
              summaryDisplay();
          });
          $(".bydaym").click(function(){
              summaryDisplay();
          });





      }
      //loadRepeatData("FREQ=WEEKLY;INTERVAL=3;BYDAY=SU,MO,FR,WE;COUNT=5");
      //var currentDay = new Date();
      //loadRepeatData("FREQ=WEEKLY;BYDAY="+weekDays[currentDay.getDay()]+"");
      if ($("#rrule").val()!="")
          loadRepeatData($("#rrule").val());



  });