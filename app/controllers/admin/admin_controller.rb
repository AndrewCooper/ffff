class Admin::AdminController < ApplicationController
  before_filter :authorize_admin

  protected
  def authorize_admin
    unless session[:user][:admin] == 1
      flash[:notice] = "Administrator access required."
      redirect_to root_path
      false
    end
  end
end
