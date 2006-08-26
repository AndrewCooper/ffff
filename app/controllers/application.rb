# Filters added to this controller will be run for all controllers in the application.
# Likewise, all the methods added will be available for all controllers.
class ApplicationController < ActionController::Base
	layout :isComponent
	before_filter :authorize_user

	#	@@games_per_week = 7

	def authorize_user
		unless session[:user]
			flash[:notice] = "Login Required."
			redirect_to :controller=>"/"
			false
		end
	end

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
	def isComponent
		@params["component"] ? nil : "standard"
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