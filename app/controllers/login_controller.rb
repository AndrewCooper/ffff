require 'digest/sha1'
require 'hmac-sha1'

class LoginController < ApplicationController
	before_filter :authorize_user,:except=>[:request_login,:login,:logout]
	
	def request_login
		@session[:challenge] = nil
		@session[:user] = nil
		@session[:challenge] = Digest::SHA1.hexdigest(rand.to_s)
		logger.info "Challenge: "+@session[:challenge]
		render :partial => "login"
	end

	def login
		user = User.find(:first,:conditions=>["login=?",@params[:login]])
		if user
			calc_response = HMAC::SHA1.hexdigest(user.password,@session[:challenge]);
			if calc_response == @params[:response]
				update_session(user)
				if user.new_password == 1
					user.update_attribute("new_password",0)
					flash[:notice] = "You are now encouraged to change your password."
					redirect_to(:controller => "/user",:action=>"profile") and return
				end
			else
				flash[:notice]= "Password incorrect for "+@params[:login]
				
				@session[:user] = nil
			end
		else
			flash[:notice] = "User "+@params[:login]+" not found."
		end
		@session[:challenge] = nil
		redirect_to :controller => "/"
	end
  
	def logout
		@session[:user] = nil
		redirect_to :controller => "/"
	end
end