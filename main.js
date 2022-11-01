
$(function(){


$('#btn').attr('href', 'javascript::onClick()')

});



function onClick(){
    let baseUrl = $('#host').val();
    var mac=  encodeURIComponent($('#mac').val());
    baseUrl += '/portal.php';

    getToken(baseUrl,mac)
    .done(function(tok){
        console,log(tok);

    });
}

function ajaxRequest(url,urlencodemac='',token=''){
    return $.ajax({
        url:url,
        type:'GET',
        headers:{
            'User-Agent':'Mozilla/5.0',
            'Cookie':'mac='+urlencodemac+'; stb_lang=en; timezone=Europe%2FParis;',
            'Authorization': 'Bearer '+ token
        },
        error: function (jqXhr, textStatus, errorMessage){
            console.log("error")
        }

    });
}
function getToken(baseUrl, urlencodemac){
    const url = baseUrl + '?action=handshake&type=stb&token=';
   return ajaxRequest(url, urlencodemac);
}

function getProfile(baseUrl,urlencodemac,token){
    const url = baseUrl + 'type=stb&action=get_profile';
    return ajaxRequest(baseUrl,urlencodemac,token);
}
function getOrderedVODList(baseUrl,urlencodemac,token){
    const url = baseUrl + 'action=get_ordered_list&type=vod&p=1&JsHttpRequest=1-xml';
    return ajaxRequest(baseUrl,urlencodemac,token);
}
function getLink(baseUrl,urlencodemac,token, base64EncodeCmd){
    const url = baseUrl + 'action=create_link&type=vod&cmd=' + base64EncodeCmd + '&JsHttpRequest=1-xml';
    return ajaxRequest(baseUrl,urlencodemac,token);
}

