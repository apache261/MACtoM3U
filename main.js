var isReady = false;
showLoader();
$(function(){
    hideLoader();
    $('#btn').attr('href', 'javascript:sendRequest()');
});

function showLoader(){
    isReady = false;
    $('#btn').hide();
    $('#loader').show();
}
function hideLoader(){
    isReady = true;
    $('#btn').show();
    $('#loader').hide();
}

function sendRequest(){
    
    if(!isReady) {return;}
    showLoader();
    const url = 'process/index.php';
    const data = convertFormToJSON('#frm');
    ajaxRequest(url,data)
    .done(function(tok){
        $('#debugArea').text(tok.data);
        hideLoader();

    });
}
function convertFormToJSON(form) {
    const array = $(form).serializeArray(); // Encodes the set of form elements as an array of names and values.
    const json = {};
    $.each(array, function () {
      json[this.name] = this.value || "";
    });
    return json;
  }


function ajaxRequest(url,data){
    return $.ajax({
        url:url,
        type:'post',
        content:'json',
        data:JSON.stringify(data),
        contentType: "application/json; charset=utf-8",
        error: function (jqXhr, textStatus, errorMessage){
            console.log("error")
        }

    });
}

