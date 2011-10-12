class Admin::BowlsController < Admin::AdminController
  # GET /admin/bowls
  def index
    @title = "Administration :: Bowls"
    @item = Bowl.new
    @items = Bowl.order("name")
    @games = Game.where(:is_bowl=>true).order("week,gametime").collect {|p| ["#{p.away_team.location} #{p.away_team.name} @ #{p.home_team.location} #{p.home_team.name}",p.id]}
  end

  # POST /admin/bowls
  def create
    @item = Bowl.create( params[:item] )
    @items = Bowl.order("name")
    respond_to do |format|
      format.html { redirect_to admin_bowls_path }
      format.js
    end
  end

  # GET /admin/bowls/new
  def new
    @item=Bowl.new
    @games = Game.where(:is_bowl=>true).order("week,gametime").collect {|p| ["#{p.away_team.location} #{p.away_team.name} @ #{p.home_team.location} #{p.home_team.name}",p.id]}
    render :action=>:edit
  end

  # GET /admin/bowls/:id/edit
  def edit
    @item = Bowl.find(params[:id])
    @games = Game.where(:is_bowl=>true).order("week,gametime").collect {|p| ["#{p.away_team.location} #{p.away_team.name} @ #{p.home_team.location} #{p.home_team.name}",p.id]}
  end

  # GET /admin/bowls/:id
  def show
    @item = Bowl.find(params[:id])
  end

  # PUT /admin/bowls/:id
  def update
    @item = Bowl.find(params[:id])
    if @item.update_attributes(params[:item])
      respond_to do |format|
        format.html { redirect_to admin_bowls_path }
        format.js { render :action=>:show }
      end
    else
      respond_to do |format|
        format.html { redirect_to edit_admin_bowl_path(params[:id]) }
        format.js { render :action=>:edit }
      end
    end
  end

  # DELETE /admin/bowls/:id
  def destroy
    @item = Bowl.find(params[:id])
    @item.destroy
    respond_to do |format|
      format.html { redirect_to admin_bowls_path }
      format.js
    end
  end
end
