require File.dirname(__FILE__) + '/../test_helper'
require 'user_controller'

# Re-raise errors caught by the controller.
class UserController; def rescue_action(e) raise e end; end

class UserControllerTest < ActionController::TestCase
	fixtures :users
	
	def setup
		@controller = UserController.new
		@request    = ActionController::TestRequest.new
		@response   = ActionController::TestResponse.new
	end

	def test_forgot_password_success
		get :forgot_password
		assert_response :success
		
		post :forgot_password, :username => users(:usera).login
		
	end
end
