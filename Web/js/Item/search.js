var currentPage = 1;
var nextPage = 2;

$(document).ready(function(){  
  
    $('#markSelect').change(function(){  
        var markName = $(this).val();
        if (markName == '') {
            $('#modelSelect').html('');
            $('#modelSelect').attr('disabled', true);
            return(false);
        }
        
        disableElements(["modelSelect","yearSelect","bodySelect"]);
        
        $.ajax({  
            type: "POST",  
            url: self.location, 
            data: "route=Item/searchByMark&requestType=JSON&selectedMark="+$('#markSelect').val(),
            success: function(result){
                var resultObject = jQuery.parseJSON(result);
                fillComboBox("modelSelect", resultObject.models);
                enableElements(["modelSelect"]);
                updateTable(resultObject.items);
            }  
        });  
    });
    
    $('#modelSelect').change(function(){  
        disableElements(["yearSelect","bodySelect"]);
        $.ajax({  
            type: "POST",  
            url: self.location, 
            data: "route=Item/searchYearsAndBodies&requestType=JSON"+
                    "&selectedMark="+$('#markSelect').val() +
                    "&selectedModel="+$('#modelSelect').val(),
            success: function(result){
                var resultObject = jQuery.parseJSON(result);
                fillComboBox("yearSelect", resultObject.years);
                fillComboBox("bodySelect", resultObject.bodies);
                enableElements(["yearSelect","bodySelect"]);
            }  
        });  
        updateItems();
    });

    $('#yearSelect').change(function(){  
        updateItems();
    });

    $('#bodySelect').change(function(){  
        updateItems();
    });

    $(window).scroll(function(){ 
        if ($(document).height() - $(window).height() <= $(window).scrollTop() + 50) { 
            appendItems();
        } 
    });
}); 

function appendItems() {
    if (currentPage + 1 != nextPage)
        return;
    $.ajax({  
        type: "POST",  
        url: self.location, 
        data: "route=Item/searchByFields&requestType=JSON"+
                "&selectedMark="+$('#markSelect').val() +
                "&selectedModel="+$('#modelSelect').val() +
                "&selectedYear="+$('#yearSelect').val() +
                "&selectedBody="+$('#bodySelect').val() +
                "&currentPage="+nextPage,
        success: function(result){
            var resultObject = jQuery.parseJSON(result);
            appendTable(resultObject.items);
        }  
    });
    nextPage++;
}

function appendTable(itemsTable) {
    if (itemsTable != '')
        currentPage++;
    $('#ItemsTableBody').append(itemsTable);
}

function updateItems() {
    currentPage = 1;
    nextPage = 2;
    $.ajax({  
        type: "POST",  
        url: self.location, 
        data: "route=Item/searchByFields&requestType=JSON"+
                "&selectedMark="+$('#markSelect').val() +
                "&selectedModel="+$('#modelSelect').val() +
                "&selectedYear="+$('#yearSelect').val() +
                "&selectedBody="+$('#bodySelect').val() +
                "&currentPage="+currentPage,
        success: function(result){
            var resultObject = jQuery.parseJSON(result);
            updateTable(resultObject.items);
        }  
    });
}

function updateTable(itemsTable) {
    $('#ItemsTableBody').html(itemsTable);
}

function fillComboBox(className, items) {
    var options = '<option value="empty"></option>';
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