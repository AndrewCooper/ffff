class Admin::GamesController < Admin::AdminController
  # GET /admin/games
  def index
    @title = "Administration :: Games"
    @item = Game.new
    @items = Game.order("week,gametime").includes(:away_team,:home_team)
    @teams = Team.order("location").collect{|t| [t.location+" "+t.name,t.id]}
  end

  # POST /admin/games
  def create
    @item = Game.create( params[:item].except("id") )
    @items = Game.order("week,gametime")
    respond_to do |format|
      format.html { redirect_to admin_games_path }
      format.js
    end
  end

  # GET /admin/games/new
  def new
    @item=Game.new
    @teams = Team.order("location").collect{|t| [t.location+" "+t.name,t.id]}
    render :action=>:edit
  end

  # GET /admin/games/:id/edit
  def edit
    @item = Game.find(params[:id])
    @teams = Team.order("location").collect{|t| [t.location+" "+t.name,t.id]}
  end

  # GET /admin/games/:id
  def show
    @item = Game.find(params[:id])
    @teams = Team.order("location").collect{|t| [t.location+" "+t.name,t.id]}
  end

  # PUT /admin/games/:id
  def update
    @item = Game.find(params[:id])
    if @item.update_attributes(params[:item])
      respond_to do |format|
        format.html { redirect_to admin_games_path }
        format.js { render :action=>:show }
      end
    else
      respond_to do |format|
        format.html { redirect_to edit_admin_game_path(params[:id]) }
        format.js do
          @teams = Team.order("location").collect{|t| [t.location+" "+t.name,t.id]}
          render :action=>:edit
        end
      end
    end
  end

  # DELETE /admin/games/:id
  def destroy
    @item = Game.find(params[:id])
    @item.destroy
    respond_to do |format|
      format.html { redirect_to admin_games_path }
      format.js
    end
  end
end
