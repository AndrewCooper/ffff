divid="#item<%= @item.id %>"
$(divid).html( "<%= j render :partial=>"edit", :locals=>{:item=>@item,:games=>@games} %>" )
#page.visual_effect( :appear, divid, :duration=>0.5 )
