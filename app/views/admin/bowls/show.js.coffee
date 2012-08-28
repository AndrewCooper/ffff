divid="#item<%= @item.id %>"
$(divid).html("<%= j render :partial=>"show", :locals=>{:item=>@item} %>")
