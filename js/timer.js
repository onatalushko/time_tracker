
var hh;
var ss;
var mm;
var time;

$(document).ready(function() {
  $('td.active_timer_time').each(function(index) {
      //alert(index + ': ' + $(this).html());
      time = $(this).html();
      hh = time.split(':')[0];
      mm = time.split(':')[1];
      ss = time.split(':')[2];
      addTime(hh, mm, ss, $(this));
    });
  // alert('started');
  // time = $('td.active_timer_time').html()
  // hh = time.split(':')[0];
  // mm = time.split(':')[1];
  // ss = time.split(':')[2];
  // addTime();
  // alert('started');
  // $('td.active_timer_time').each(function(){
  //     alert(this.html());
  //     time = this.html();
  //     hh = time.split(':')[0];
  //     mm = time.split(':')[1];
  //     ss = time.split(':')[2];
  //     addTime(hh, mm, ss);
  // })
});

/*
  Adds a second of time
*/
function addTime(hh, mm, ss, el) {
  //alert(pad(hh, 2) + ":" + pad(mm, 2) + ":" + pad(ss, 2));
  ss++;
  if(ss == 60) {
    ss = 0;
    mm++;
  }
  if (mm == 60) {
    mm = 0;
    hh++;
  }
  var func = function () {
    addTime(hh,mm,ss,el);
  }
  setTimeout(func, 1000);
  el.html(pad(hh, 2) + ":" + pad(mm, 2) + ":" + pad(ss, 2));
}

/*
  Pad a number with zeroes
*/
function pad(number, length) {
    var str = '' + number;
    while (str.length < length) {
        str = '0' + str;
    }
    return str;
}






// 
// 
// function cd() {
//    mins = 1 * m("10"); // change minutes here
//    secs = 0 + s(":01"); // change seconds here (always add an additional second to your total)
//    redo();
// }
// 
// function m(obj) {
//    for(var i = 0; i < obj.length; i++) {
//      if(obj.substring(i, i + 1) == ":")
//      break;
//    }
//    return(obj.substring(0, i));
// }
// 
// function s(obj) {
//    for(var i = 0; i < obj.length; i++) {
//      if(obj.substring(i, i + 1) == ":")
//      break;
//    }
//    return(obj.substring(i + 1, obj.length));
// }
// 
// function dis(mins,secs) {
//    var disp;
//    if(mins <= 9) {
//      disp = " 0";
//    } else {
//      disp = " ";
//    }
//    disp += mins + ":";
//    if(secs <= 9) {
//      disp += "0" + secs;
//    } else {
//      disp += secs;
//    }
//    return(disp);
// }
// 
// function redo() {
//    secs--;
//    if(secs == -1) {
//      secs = 59;
//      mins--;
//    }
//    document.cd.disp.value = dis(mins,secs); // setup additional displays here.
//    if((mins == 0) && (secs == 0)) {
//      window.alert("Time is up. Press OK to continue."); // change timeout message as required
//      // window.location = "yourpage.htm" // redirects to specified page once timer ends and ok button is pressed
//    } else {
//      cd = setTimeout("redo()",1000);
//    }
// }
// 
// function init() {
//   cd();
// }
// window.onload = init;



