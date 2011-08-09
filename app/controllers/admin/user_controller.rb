class Admin::UserController < Admin::AdminController  
  def delete
    User.delete(params[:id])
    #     Pick.delete_all("user_id = #{params[:id]}")
    #     Score.delete_all("user_id = #{params[:id]}")
    render :nothing=>true
  end

  def edit
    @colspan = 7
    if params[:id]
      @item = User.find(params[:id])
    else
      @item = User.new
    end
    render :partial => "edit"
  end

  def index
    @title = "Administration :: Users"
  end

  def list
    if params[:id]
      @item = User.find(params[:id])
      render(:partial => "item") and return
    else
      @items = User.find(:all,:order=>"lastname,firstname")
    end
  end

  def new
    @item=User.new
    render :partial=>"admin/shared/newitem"
  end

  def update
    if params[:id]
      @item = User.find(params[:id])
      if @item.update_attributes(params[:item])
        redirect_to :action => "list",:id=>params[:id]
      else
        redirect_to :action => "edit",:id=>params[:id]
      end
    else
      @item = User.new(params[:item])
      if @item.save
        redirect_to :action => "list","component"=>true
      else
        logger.error "Errors: "+@item.errors.inspect
        render :text => (@item.errors.inspect  + "\n")
      end
    end
  end
end
