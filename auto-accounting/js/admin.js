jQuery(function( $ ){
	$(document).ready(function(){
		if($('#wpasaledate').length){
			$('#wpasaledate').closest('tr').before('<tr id="totalsales"><td>Total Sales: </td><td>$0</td></tr>');
			$('input[name^="sales"]').keyup(function(){
				calcSales();
			});
			calcSales();
		}
		$('a.confirm').click(function(){
			if(confirm("Are you sure you want to do this?")){
				location.href=$(this).attr('href');
			}
			return false;
		});
		
		$('input.expander').click(function(){
			var tbl = $('table',$(this).parent('td'));
			if(tbl.is(":visible")){
				tbl.hide();
				$(this).val('+');
			}else{
				tbl.show();
				$(this).val('-');
			}
			$(this).trigger('expand');
		});
		$('input.expandall').click(function(){
			var expand = $(this).val().substr(0,1) == '+';
			$(this).val(!expand ? '+ Expand All' : '- Hide All');
			$('input.expander').each(function(){
				var tbl = $('table',$(this).parent('td'));
				if(!expand){
					tbl.hide();
					$(this).val('+');
				}else{
					tbl.show();
					$(this).val('-');
				}
			});
		}).trigger('click');
	});
	
	function calcSales(){
			var sales = 0;
			$('input[name^="sales"]').each(function(){
				var val = $(this).val();
				if($.isNumeric(val)){
					val = parseFloat(val);
					if($(this).hasClass('less')){
						sales -= val;
					}else{
						sales += val;
					}
				}
			});
			$('#totalsales td:eq(1)').text('= '+sales.toFixed(2));
	}
});