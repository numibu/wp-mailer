var $ = jQuery;
// set in php script admin_url('admin-ajax.php');
var admin_ajax_url = window.admin_ajax_url;

/**
 * функция отправки данных на сервер.
 * @param {string} url
 * @param {DataForm} data
 * @param {function} callBackFunction
 * @returns {void} */
function AJAX(url, data, callBackFunction) {
    var data = data || null;
    var func = callBackFunction || null;
    var onAjaxError = function(e) {console.log(e);};

    $.ajax({
        url: url,
        data: data,
        processData: false,
        contentType: false,
        type: 'POST',
        success: func,
        error: onAjaxError
    });
}

function clickToContentItem(typePost, idPost){
    
    var inputs = $('input[name="client"]:checked');
    
    if ( inputs.length > 0) {
        
        var requestItemsArray = new Array();
        var requestCustomItemsArray = new Array();
        var data = new FormData();
        var dataCustom = new FormData();///TEST
        data.isSend = false;
        dataCustom.isSend = false; 
    
        $.each(inputs, function(index, value){
            
            if ( $(value).attr('user-type') === "custom" ){
                var item = fillCustomUserItem(index, value, typePost, idPost);
                requestCustomItemsArray.push(item);
                dataCustom.append('action', 'addCustomTaskToMailer');//add AJAX action for php script
                dataCustom.append('requestCustomItemsArray', JSON.stringify(requestCustomItemsArray));
                dataCustom.isSend = true ;
            }else{
                var item = fillUserItem(index, value, typePost, idPost);
                requestItemsArray.push(item);
                data.append('action', 'addTaskToMailer');//add AJAX action for php script
                data.append('requestItemsArray', JSON.stringify(requestItemsArray));
                data.isSend = true ;
            }

        });
        
        if (data.isSend) { AJAX(admin_ajax_url, data, updateTaskGrid); }
        if (dataCustom.isSend) { AJAX(admin_ajax_url, dataCustom, updateTaskGrid); }
    }
}

function addToListAddresse(){
    
    var name = $('input[name="customAddresseName"]').val();
    var mail = $('input[name="customAddresseMail"]').val();
    
    if(name.length < 1 && name.length > 30){ alert("Имя меньше одного символа!!!"); return;}
    if(!validateEmail(mail)){ alert("Mail не валиден!!!"); return;}
    
    var ul = $('ul.addresseeList');
    
    var input = $('<input>');
    input.attr("type","checkbox");
    input.attr("name","client");
    input.attr("user-type","custom");
    input.attr("user-name",name);
    input.attr("user-mail",mail);
    
    var li = $('<li>'); //li.text(' login: ----; name: '+name+'; mail: '+mail);
    var nameSTR = "<strong> name: </strong> <span style='color: green;'> "+name+" </span>,";
    var emailSTR = "<strong> email: </strong> <span style='color: blue;'> "+mail+" </span></li>";
    li.html(' login: ----; ' + nameSTR + emailSTR);
    input.prependTo(li);
    li.appendTo(ul);    
}

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function fillCustomUserItem(index, value, typePost, idPost){
    var item = {};
    item.addressee_name = ($(value).attr('user-name'));
    item.addressee_mail = ($(value).attr('user-mail'));
    item.type = typePost;
    item.post_id = idPost;
    return item;
}

function fillUserItem(index, value, typePost, idPost){
    var item = {};
    item.addressee_id = ($(value).attr('user-id'))*1;
    item.type = typePost;
    item.post_id = idPost;
    return item;
}

function deleteTask(userType, taskId){
    var data = new FormData();
    if ( userType === 'custom' ){
        data.append('type', 'custom');
    }else{
        data.append('type', 'register');
    }
    data.append('action', 'deleteTask');
    data.append('id', taskId);
    
    AJAX(admin_ajax_url, data, updateTaskGrid);
}

function sendCell(userType, userMail){
    var data = new FormData();
    if ( userType === 'custom' ){
        data.append('type', 'custom');
    }else{
        data.append('type', 'register');
    }
    
    data.append('action', 'send');
    data.append('mail', userMail);
    
    AJAX(admin_ajax_url, data, updateTaskGrid);
}

function updateTaskGrid(response){
    //console.dir(response);
    var error = response.error;
    
    if ( !isPropObject(error) && !('body' in response)){
        var data = new FormData();
        data.append('action', 'getTaskGrid');
        AJAX(admin_ajax_url, data, updateTaskGrid);
    }
    if (('body' in response)) {
        var body = response.body;
        if ( 'taskGrid' in body ){
            var html = body.taskGrid;
            $('table.taskList').remove();
            $(html).appendTo('#content3');
            var count = ($('#content3 .sendRow')).length;
            $('span.allTask').html('('+count+')');
        }
    }
}

function isPropObject(obj) {
    for (var i in obj) {
        if ( obj.hasOwnProperty(i) ) {
            return true;
        }
    }
    return false;
}