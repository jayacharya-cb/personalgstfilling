var TableQuickInvoiceItemDatatablesAjax = function () {
	var table_container=$("#quick_invoice_item_container");
	var ajax_url="quick_invoice_item_get_ajax.php";
	var modification_url="#";
	var show_count=100;
	var query="";
	var page=1;
    var handleTable = function () {
		var table=$("#quick_invoice_item_table_id");
		var table_container=$("#quick_invoice_item_container");
		var pagination_buttons=$(table_container).find('.quick_invoice_item_pagination').children("li.paginate_button").find("a");
		var searchBtn=$(table_container).find('button.searchBtn');
		var clearBtn=$(table_container).find('button.clearBtn');
		var editBtn=$(table_container).find('a.quick_invoice_item_table_id_edit');
		var deleteBtn=$(table_container).find('a.quick_invoice_item_table_id_delete');
		var activeBtn=$(table_container).find('a.quick_invoice_item_table_id_active');
		var deactiveBtn=$(table_container).find('a.quick_invoice_item_table_id_deactive');
		
		var row_count_spinner=$(table_container).find("select.rowCountSpinner");
		$(table).dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		});
		$(searchBtn).on('click',function(){
			query=$("#quick_invoice_item_search_input").val();
			getDataFromAJAX();
		});
		$(editBtn).on('click',function(){
			var id=$(this).data("id");
			var args=[];
			args={id:id,MODE:"e"};
			submitTo(args);
		});
		$(deleteBtn).on('click',function(){
			var id=$(this).data("id");
			var args=[];
			args={id:id,MODE:"d","submit_form":1};
			submitTo(args);
		});
		$(activeBtn).on('click',function(){
			var id=$(this).data("id");
			var args=[];
			args={id:id,MODE:"ac",status:1,"submit_form":1};
			submitTo(args);
		});
		$(deactiveBtn).on('click',function(){
			var id=$(this).data("id");
			var args=[];
			args={id:id,MODE:"ac",status:0,"submit_form":1};
			submitTo(args);
		});
		$(clearBtn).on('click',function(){
			query="";
			getDataFromAJAX();
		});
		$(pagination_buttons).on('click',function(){
			page=$(this).data("page");
			getDataFromAJAX();
		});
		$(row_count_spinner).on("change",function(){
			show_count=$(this).val();
			getDataFromAJAX();
		});
    }
	var submitTo=function(args)
	{
		
        var form = $('<form></form>');
        form.attr("method", "post");
        form.attr("action", modification_url);

        $.each( args, function( key, value ) {
            var field = $('<input></input>');

            field.attr("type", "hidden");
            field.attr("name", key);
            field.attr("value", value);

            form.append(field);
        });
        $(form).appendTo('body').submit();
	}
	var getDataFromAJAX=function(){
		
		$.ajax({
			url:ajax_url,
			data:{
				page:page,
				show:show_count,
				query:query
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
    TableQuickInvoiceItemDatatablesAjax.init();
});