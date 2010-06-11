var profiles =
{
  window800:
    {
      height:500,
      width:500,
      status:1
    },
};

function unloadcallback(){
  alert("unloaded");
};

$(function()
{
  $(".timer").popupwindow(profiles);
});

/*
height:600, // sets the height in pixels of the window.
width:600, // sets the width in pixels of the window.
toolbar:0, // determines whether a toolbar (includes the forward and back buttons) is displayed {1 (YES) or 0 (NO)}.
scrollbars:0, // determines whether scrollbars appear on the window {1 (YES) or 0 (NO)}.
status:0, // whether a status line appears at the bottom of the window {1 (YES) or 0 (NO)}.
resizable:1, // whether the window can be resized {1 (YES) or 0 (NO)}. Can also be overloaded using resizable.
left:0, // left position when the window appears.
top:0, // top position when the window appears.
center:0, // should we center the window? {1 (YES) or 0 (NO)}. overrides top and left
createnew:1, // should we create a new window for each occurance {1 (YES) or 0 (NO)}.
location:0, // determines whether the address bar is displayed {1 (YES) or 0 (NO)}.
menubar:0, // determines whether the menu bar is displayed {1 (YES) or 0 (NO)}.
onUnload:null // function to call when the window is closed
*/