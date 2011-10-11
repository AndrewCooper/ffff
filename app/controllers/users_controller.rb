class UsersController < ApplicationController
  before_filter :authorize_user,:except=>[:forgot_password,:reset_password]

  # GET /user/edit
  def edit
    @title = "FFFF :: Edit Profile"
    @item = User.find(session[:user][:uid])
  end

  # PUT /user
  def update
    @item = User.find(session[:user][:uid])

    params[:user][:password] = ""
    if params[:password][:new] != ""
      if params[:password][:new] == params[:password][:confirm]
        params[:user][:password] = params[:password][:new]
      else
        flash.now[:warning] = "New password does not match confirmation."
      end
    end

    @item.update_attributes( params[:user] )
    flash[:notice] = "Profile Successfully Updated"
    update_session(@item)
    redirect_to :action=>:edit
  end

  # GET /user/forgot_password
  def forgot_password
    @title = "FFFF :: Forgot Password"
  end

  # PUT /user/forgot_password
  def reset_password
    user = User.where("login = ?",params[:username]).first
    if user.nil?
      flash[:warning] = render_to_string( :partial=>"password_no_user", :locals=>{:username=>params[:username]} )
    else
      newpass = random_password
      Notifications.forgot_password(user,newpass).deliver
      flash[:notice] = "The email has been successfully sent."
      user.password = newpass
      user.new_password = true
      user.save
    end
    redirect_to :action=>:forgot_password
  end
end
