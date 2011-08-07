# Filters added to this controller apply to all controllers in the application.
# Likewise, all the methods added will be available for all controllers.
class ApplicationController < ActionController::Base
  helper :all # include all helpers, all the time
  #	init_gettext "ffff"
  layout :is_component
  before_filter :authorize_user

  # See ActionController::RequestForgeryProtection for details
  # Uncomment the :secret if you're not using the cookie session store
  protect_from_forgery # :secret => 'd230359592a4ee2e53e80210c43f9e70'

  # See ActionController::Base for details
  # Uncomment this to filter the contents of submitted sensitive data parameters
  # from your application log (in this case, all fields with names like "password").
  # filter_parameter_logging :password

  #	@@games_per_week = 7

  def authorize_user
    unless session[:user]
      flash[:notice] = "Login Required."
      redirect_to root_path
      false
    end
  end

  private
  def update_session(user = nil)
    if !user
      if session[:user]
        user = User.find(session[:user][:uid])
      end
    end
    if user
      session[:user] = user.session_info
      session[:user][:stats] = Score.user_stats(user[:id])
    end
  end

  private
  def is_component
    params["component"] ? nil : "standard"
  end

  def time_zone
    if Time.new.dst?
      FFFF_SERVER_TIMEZONE[1]
    else
      FFFF_SERVER_TIMEZONE[0]
    end
  end

  def current_app_time
    Time.new+FFFF_SERVER_OFFSET
  end

  def random_password(size = 8)
    chars = (('a'..'z').to_a + ('a'..'z').to_a + ('0'..'9').to_a)
    (1..size).collect{|a| chars[rand(chars.size)] }.join
  end

end
