$( document ).ready(function() {
    //toggleFullscreen();
});

//function loader
//- 01/04/2019 supawat
//- success
function JSxContentLoader(ptType){
    if(ptType == 'show'){
        $('.odvLoaderprogress').fadeIn('slow');
    }else if(ptType == 'hide'){
        setTimeout(function(){
            $('.odvLoaderprogress').fadeOut('slow');
        }, 500);
    }
}

//function check session
//- 01/04/2019 supawat
//- success
function JSxCheckSession(pnParam){
    if(pnParam == 'session_expired'){
        window.location.href = 'content.php?route=login';
    }
}

function toggleFullscreen(elem) {
    elem = elem || document.documentElement;
    if (!document.fullscreenElement && !document.mozFullScreenElement &&
      !document.webkitFullscreenElement && !document.msFullscreenElement) {
      if (elem.requestFullscreen) {
        elem.requestFullscreen();
      } else if (elem.msRequestFullscreen) {
        elem.msRequestFullscreen();
      } else if (elem.mozRequestFullScreen) {
        elem.mozRequestFullScreen();
      } else if (elem.webkitRequestFullscreen) {
        elem.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
      }
    } else {
      if (document.exitFullscreen) {
        document.exitFullscreen();
      } else if (document.msExitFullscreen) {
        document.msExitFullscreen();
      } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen();
      } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
      }
    }
  }

  //Remove Sort by Column
function JSxRemoveValueSortByColumn(){
  $('#ohdNameSort').val('');
  $('#ohdTypeSort').val('');
}

//loop เพื่อนำ sql message error ออกมาแสดง
function JCNxDisplayErrorSQL(paMessage){
    if(Array.isArray(paMessage) && paMessage.length > 0){
        var tSQLError = "";
        for(var i = 0; i < paMessage.length; i++ ){
            if(Array.isArray(paMessage[i]) && paMessage[i].length > 0){
                for(var a = 0; a < paMessage[i].length; a++ ){
                    if(Array.isArray(paMessage[i][a]) && paMessage[i][a] !== null && paMessage[i][a].length > 0){
                        for(var b = 0; b < paMessage[i][a].length; b++ ){
                            tSQLError += "<div class='alert alert-danger' role='alert'>" + paMessage[i][a][b]['message'] + "</div>";
                        }
                    }
                }
            }
        }

        if(tSQLError != ""){
          var aTextConCode = {
              tHead       : 'SQL Error',
              tDetail     : tSQLError
          };
          JCNxDialogMessage(aTextConCode);
        }
    }
}

function JCNxDialogMessage(aPackDataMesage){
    JSxContentLoader('hide');

    $('#odvModalDialogMessage').modal('show');
    $('.xCNModalDialogMessageHead').html(aPackDataMesage['tHead']);
    $('.xCNModalDialogMessageBody').show().html(aPackDataMesage['tDetail']);

    $('#odvModalDialogMessage').off('keyup');
    $('#odvModalDialogMessage').on('keyup', function(e){
        if(e.keyCode === 13) {
            $('.xCNCloseDialogMessage').click();
        }
    });

}