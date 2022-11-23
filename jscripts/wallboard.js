/**
 * File to handle javascrip operations in wallboard section
 */

/**
 * GLOBAL VARIABLES
 */
var SYNC_TIME = 30000;
var AJAXURL = '../../../../application/ajax/accounts.php';
var REQUSET_POST = 'POST';
var DATA_TYPE = 'JSON';
/**
 * Function to handle drag and drop list items
 * 
 * @author Chetu Inc.
 * @returns void 
 */
$(function() {
    $( ".connectedSortable" ).sortable({
        connectWith: ".connectedSortable",
        receive: function (event, ui) {             
             $(".connectedSortable").not(this).append();
             makeAgentList();
        }
    }).disableSelection();
});

/**
 * Function to generate hash based URL for detail page of dash board
 * 
 * @author Cherlie
 * @returns void 
 */
function generateHash(){
    var hash = $.md5("Wallboard hash "+Date.now());  
     $("#hash").val(hash);
}

/**
 * Function to manage agent list for posting agents in php
 * 
 * @author Charlie
 * @returns void
 */
function makeAgentList(){
    $("#addAgentListDiv").html("");
    $( "#addedAgents li" ).each(function( index ) {
        var agentName = $( this ).text();
        $("#addAgentListDiv").append("<input type='hidden' name='agentList[]' value='"+$(this).find('input').val()+"'>");
        $("#addAgentListDiv").append("<input type='hidden' name='agentName[]' value='"+agentName+"'>");
    });
}

/**
 * Functionality to export data from detail page to excel sheet
 * 
 * @returns Excel file as download
 */
$(function () {
    $("#export-table").click(function () {
        $("#detail-table").table2excel({
            filename: "Table.xls"
        });
    });
});


/**
 * Auto syncer for detail page
 * 
 * @return updated UI
 */
function sync(){    
    $.ajax({
        url: AJAXURL,
        type: REQUSET_POST,
        dataType: DATA_TYPE,
        async: false,
        data: {            
            action: 'sync-wallboard',
            hash:$("#hash").val()
        },
        success: function (data) {
            $("#wallboard-table-body").html("");
            $("#wallboard-table-body").html(data.updatedUI);
        }
    });
}

/**
 * Sync functionality settings
 */
$(document).ready(function(){
    setInterval(function(){
        sync();
    },SYNC_TIME);    
});