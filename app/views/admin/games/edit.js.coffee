divid="#item<%= @item.id %>"
$(divid).html("<%= j render :partial=>"edit", :locals=>{:item=>@item,:teams=>@teams}  %>")
#page.visual_effect( :appear, divid, :duration=>0.5 )
