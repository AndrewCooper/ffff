class Admin::GamesController < Admin::AdminController
  # GET /admin/games
  def index
    @title = "Administration :: Games"
    @item = Game.new
    @items = Game.find_with_teamnames(:order=>"week,gametime")
    @teams = Team.find_by_sql("SELECT location,name,id FROM teams ORDER BY location").collect{|t| [t.location+" "+t.name,t.id]}
  end

  # GET /admin/games/:id
  def show
    @item = Game.find_with_teamnames(:conditions=>"games.id = #{params[:id]}").first
    @teams = Team.find_by_sql("SELECT location,name,id FROM teams ORDER BY location").collect{|t| [t.location+" "+t.name,t.id]}
  end

  # GET /admin/games/new
  def new
    @item=Game.new
  end

  # GET /admin/games/:id/edit
  def edit
    @item = Game.find_with_teamnames(:conditions=>"games.id = #{params[:id]}").first
    @teams = Team.find_by_sql("SELECT location,name,id FROM teams ORDER BY location").collect{|t| [t.location+" "+t.name,t.id]}
  end

  # POST /admin/games
  def create
    @item = Game.create( params[:item] )
    @items = Game.find_with_teamnames(:order=>"week,gametime")
    respond_to do |format|
      format.html { redirect_to admin_games_path }
      format.js
    end
  end

  # PUT /admin/games/:id
  def update
    @item = Game.find_with_teamnames(:conditions=>"games.id = #{params[:id]}").first
    if @item.update_attributes(params[:item])
      respond_to do |format|
        format.html { redirect_to admin_games_path }
        format.js { render :action=>:show }
      end
    else
      respond_to do |format|
        format.html { redirect_to edit_admin_game_path(params[:id]) }
        format.js { render :action=>:edit }
      end
    end
  end

  # DELETE /admin/games/:id
  def destroy
    @item = Game.find_with_teamnames(:conditions=>"games.id = #{params[:id]}").first
    @item.destroy
    respond_to do |format|
      format.html { redirect_to admin_games_path }
      format.js
    end
  end
end
