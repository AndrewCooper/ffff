class Admin::AdminController < ApplicationController
	before_filter :authorize_admin

  def index
		@title = "Administration :: Configuration"
  end

	def authorize_admin
		unless session[:user][:admin] == 1
			flash[:notice] = "Administrator access required."
			redirect_to :controller=>"/"
			false
		end
	end
	end
