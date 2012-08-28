class Admin::UsersController < Admin::AdminController
  # GET /admin/users
  def index
    @title = "Administration :: Users"
    @item = User.new
    @items = User.order("lastname,firstname")
  end
  
  # POST /admin/users
  def create
    @item = User.create(params[:item])
    @items = User.order("lastname,firstname")
    respond_to do |format|
      format.html { redirect_to admin_users_path }
      format.js
    end
  end

  # GET /admin/users/new
  def new
    @item=User.new
    render :action=>:edit
  end

  # GET /admin/users/:id/edit
  def edit
    @item = User.find(params[:id])
  end

  # GET /admin/users:id
  def show
    @item = User.find(params[:id])
  end

  # PUT /admin/users/:id
  def update
    @item = User.find(params[:id])
    if @item.update_attributes(params[:item])
      respond_to do |format|
        format.html { redirect_to admin_users_path }
        format.js { render :action=>:show }
      end
    else
      respond_to do |format|
        format.html { redirect_to edit_admin_user_path(params[:id]) }
        format.js { render :action=>:edit }
      end
    end
  end

  # DELETE /admin/users/:id
  def destroy
    @item = User.find(params[:id])
    @item.destroy
    respond_to do |format|
      format.html { redirect_to admin_users_path }
      format.js
    end
  end
end
