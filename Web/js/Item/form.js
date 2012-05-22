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
            fillComboBox("modelSelect", models, 1);
            enableElements(["modelSelect"]);
        });
        
        updateItems();
    });
}); 
