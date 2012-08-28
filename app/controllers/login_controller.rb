require 'openssl'

class LoginController < ApplicationController
  include LoginResponseCalculation
  before_filter :authorize_user,:except=>[:request_login,:login,:logout]

  # GET /login
  def request_login
    session[:user] = nil
    session[:challenge] = OpenSSL::Digest::SHA1.hexdigest(rand.to_s)
    respond_to do |format|
      format.html { redirect_to root_path }
      format.js
    end
  end

  # POST /login
  def login
    redir_path = root_path
    user = User.find(:first,:conditions=>["login=?",params[:login]])
    if user
      calc_response = calculate_login_response( user.password, session[:challenge] )
      if calc_response == params[:response]
        update_session(user)
        if user.new_password?
          user.update_attribute("new_password",0)
          flash[:notice] = "You are now encouraged to change your password."
          redir_path = edit_user_path
        end
      else
        flash[:notice]= "Password incorrect for #{params[:login]}"
        session[:user] = nil
      end
    else
      flash[:notice] = "User #{params[:login]} not found."
    end
    session[:challenge] = nil
    redirect_to redir_path
  end

  # GET /logout
  def logout
    session[:user] = nil
    redirect_to root_path
  end
end
