# Filters added to this controller will be run for all controllers in the application.
# Likewise, all the methods added will be available for all controllers.
class ApplicationController < ActionController::Base
	layout :isComponent
	before_filter :authorize_user

	#	@@games_per_week = 7
	@@application_offset = 3*3600
	@@application_timezone = ["EST","EDT"]

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
			@@application_timezone[1]
		else
			@@application_timezone[0]
		end
	end

	def current_app_time
		Time.at(Time.new+@@application_offset)
	end
end