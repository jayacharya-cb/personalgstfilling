var TablefeedDatatablesAjax = function () {
	var table_container=$("#feed_container");
	var ajax_url="feed_get_ajax.php";
	var show_count=10;
	var query="";
	var page=1;
    var handleTable = function () {
		var table=$("#feed_table_id");
		alert($("#feed_container").length);
		var pagination_buttons=$("#feed_container").find('.feed_pagination').children("li.paginate_button");
		var searchBtn=$("#feed_container").find('button.searchBtn');
		var clearBtn=$("#feed_container").find('button.clearBtn');
		var row_count_spinner=$("#feed_container").find("select.rowCountSpinner");
		$(table).dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		});
		$(searchBtn).on('click',function(){
			query=$(this).closest("input[type=text]").val();
			getDataFromAJAX();
		});
		$(clearBtn).on('click',function(){
			query="";
			getDataFromAJAX();
		});
		$(row_count_spinner).on("change",function(){
			show_count=$(this).val();
			getDataFromAJAX();
		});
    }
	
	var getDataFromAJAX=function(){
		
		$.ajax({
			url:ajax_url,
			data:{
				page:page,
				show:show_count,
				searchName:query
			},
			beforeSend:function(){
				$(table_container).html("<div class='row text-center'><div class='col-sm-12'><h2><i class='fa fa-refresh fa-spin'></i>&nbsp;Loading..</h2></div></div>");
			},
			success:function(result)
			{
				$(table_container).html(result);
				handleTable();
			}
		})
		
	}

    return {

        //main function to initiate the module
        init: function () {         
            getDataFromAJAX();
        }

    };

}();

jQuery(document).ready(function() {
    TablefeedDatatablesAjax.init();
});