class Admin::GameController < Admin::AdminController
  def delete
	Game.delete(params[:id])
	render :nothing=>true
  end

  def edit
	@colspan = 6
	if params[:id]
	  @item = Game.find_with_teamnames(:conditions=>"games.id = #{params[:id]}").first
	else
	  @item = Game.new
	  @item.gametime = current_app_time
	end
	@teams = Team.find_by_sql("SELECT location,name,id FROM teams ORDER BY location").collect{|t| [t.location+" "+t.name,t.id]}
	render :partial => "edit"
  end

  def index
	@title = "Administration :: Games"
  end
	
  def list
  	if params[:id]
	  @item = Game.find_with_teamnames(:conditions=>"games.id = #{params[:id]}").first
	  render(:partial => "item") and return
  	else
	  @items = Game.find_with_teamnames(:order=>"week,gametime")
  	end
  end

  def new
  	@item=Game.new
  	render :partial=>"admin/shared/newitem"
  end
	
  def update
	if params[:id]
	  @item = Game.find(params[:id])
	  if @item.update_attributes(params[:item])
		redirect_to :action => "list",:id=>params[:id]
	  else
		render :text=>(@item.errors.inspect  + "\n")
	  end
	else
	  @item = Game.new(params[:item])
	  if @item.save
		redirect_to :action => "list","component"=>true
	  else
		logger.info "Errors: "+@item.errors.inspect
		render :text=>(@item.errors.inspect  + "\n")
	  end
	end
  end
end
