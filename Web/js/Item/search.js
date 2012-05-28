var currentPage = 1;
var nextPage = 2;

$(document).ready(function(){  
  
    $('#markSelect').change(function(){  
        var markName = $(this).val();
        if (markName == '') {
            disableElements(["modelSelect","yearSelect","bodySelect"]);
            return(false);
        }
        
        disableElements(["modelSelect","yearSelect","bodySelect"]);
        
        getModelsByMark($('#markSelect').val(), function(models){
            fillComboBox("modelSelect", models);
            enableElements(["modelSelect"]);
        });
        
        updateItems();
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

    $('#lineDirecitonSelect').change(function(){  
        updateItems();
    });

    $('#handDirecitonSelect').change(function(){  
        updateItems();
    });

    $('#brandNumberInput').change(function(){  
        updateItems();
    });

    $('#existanceSelect').change(function(){
        updateItems();
    });

    $('#hsaTypeSelect').change(function() {
        updateItems();
    })

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
                "&selectedLineDirection="+$('#lineDirecitonSelect').val() +
                "&selectedHandDirection="+$('#handDirecitonSelect').val() +
                "&selectedBrandNumber="+$('#brandNumberInput').val() +
                "&selectedExistance="+$('#existanceSelect').val() +
                "&selectedHSAType="+$('#hsaTypeSelect').val() +
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
                "&selectedLineDirection="+$('#lineDirecitonSelect').val() +
                "&selectedHandDirection="+$('#handDirecitonSelect').val() +
                "&selectedBrandNumber="+$('#brandNumberInput').val() +
                "&selectedExistance="+$('#existanceSelect').val() +
                "&selectedHSAType="+$('#hsaTypeSelect').val() +
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


