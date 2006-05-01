class UserController < ApplicationController
	def profile
		@title = "FFFF :: Edit Profile"
		if request.get?
			@item = User.find (session[:user][:uid])
		else
			@item = User.find(session[:user][:uid])

			if @params["newpassword"] != "" 
				if @params["newpassword"] == @params["newpassconf"]
					@item.password = @params["newpassword"]
				else
					flash.now[:notice] = "New password does not match confirmation."
				end
			else
				@item.password = ""
			end
			if @item.update_attributes(@params[:item])
				flash.now[:notice] = "Profile Successfully Updated"
				update_session(@item)
			else
				flash[:notice] = ""
				@item.errors.each do |type,msg|
					flash.now[:notice] += type.humanize+" "+msg
				end
			end
		end
	end
	
	def changepass
		@title = "FFFF :: Change Password"
		if request.get?
		else
			user = User.find(session[:user][:uid])
			if @params["newpassword"] == @params["newpassconf"]
				user.password = @params["newpassword"]
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
