class UserController < ApplicationController
  before_filter :authorize_user,:except=>[:forgot_password]

  def forgot_password
    @title = "FFFF :: Forgot Password"
    if request.post?
      user = User.find(:first,:conditions=>["login = ?",params[:username]])
      if user.nil?
        flash[:warning] = "There is no user with a login of #{params[:username]}.<br /> If you feel this is an error, please contact an administrator at <a href=\"mailto:admin@hkcreations.org\">admin@hkcreations.org</a>."
        return
      end
      newpass = random_password
      begin
        Notifications.deliver_forgot_password(user,newpass)
      rescue Exception => e
        logger.info "Exception: "+e.inspect
        flash[:warning] = "I'm sorry, a delivery error occured while trying to send your email.<br />Please contact an administrator at <a href=\"mailto:admin@hkcreations.org\">admin@hkcreations.org</a>."
      else
        flash[:notice] = "The email has been successfully sent."
        user.password = newpass
        user.new_password = true
        user.save
      end
    end
  end

  def profile
    @title = "FFFF :: Edit Profile"
    if request.get?
      @item = User.find(session[:user][:uid])
    else
      @item = User.find(session[:user][:uid])

      if params["newpassword"] != ""
        if params["newpassword"] == params["newpassconf"]
          @item.password = params["newpassword"]
        else
          flash.now[:warning] = "New password does not match confirmation."
        end
      else
        @item.password = ""
      end

      @item.login = params[:item][:login]
      @item.firstname = params[:item][:firstname]
      @item.lastname = params[:item][:lastname]
      @item.email = params[:item][:email]
      @item.alerts = params[:item][:alerts]
      logger.info "User: "+@item.inspect

      if @item.save
        flash.now[:notice] = "Profile Successfully Updated"
        update_session(@item)
      else
        logger.error "something bad happened: "+@item.errors.inspect
        flash[:warning] = ""
        @item.errors.each do |type,msg|
          flash[:warning] += type.humanize+" "+msg
        end
      end
    end
  end

  def changepass
    @title = "FFFF :: Change Password"
    if request.get?
    else
      user = User.find(session[:user][:uid])
      if params["newpassword"] == params["newpassconf"]
        user.password = params["newpassword"]
        if user.save
          flash[:notice] = "Password changed."
        else
          flash[:notice] = ""
          user.errors.each do |type,msg|
            flash[:notice] += type.humanize+" "+msg
          end
        end
      else
        flash[:notice] = "New password does not match confirmation."
      end
    end
    redirect_to :action=>"profile"
  end
end
