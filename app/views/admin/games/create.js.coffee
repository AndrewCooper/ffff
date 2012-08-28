$('#item_index').html("<%= j render :partial=>"index", :locals=>{:items=>@items}  %>")
