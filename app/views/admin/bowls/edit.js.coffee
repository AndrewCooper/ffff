$(<%= "item#{@item.id}" %>).html( "<%= j render :partial=>"edit", :locals=>{:item=>@item,:games=>@games} %>" )
#page.visual_effect( :appear, divid, :duration=>0.5 )
