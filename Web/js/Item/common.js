function getModelsByMark(mark, callback) {
    $.ajax({  
        type: "POST",  
        url: self.location, 
        data: "route=Item/searchModels&requestType=JSON&selectedMark="+mark,
        success: function(result){
            var resultObject = jQuery.parseJSON(result);
            callback(resultObject.models);
        }  
    });  
}

function fillComboBox(className, items, canEmpty) {
    if (typeof canEmpty == "undefined")
        canEmpty = true;
    var options = '';
    if (canEmpty)
         options += '<option value="empty"></option>';
    //$(items).each(function() {
    $.each(items,function(key, value) {
        options += '<option value="' + value + '">' + value + '</option>';
    });
    $('#' +className).html(options);
}

function enableElements(classNames) {
    $.each(classNames,function(key, value) {
        $('#'+value).attr('disabled', false);
    });
}

function disableElements(classNames) {
    $.each(classNames,function(key, value) {
        $('#'+value).attr('disabled', true);
        $('#'+value).html('<option></option>');
    });
}