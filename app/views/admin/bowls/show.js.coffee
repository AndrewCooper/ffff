$(<%= "item#{@item.id}" %>).html("<%= j render :partial=>"show", :locals=>{:item=>@item} %>")
