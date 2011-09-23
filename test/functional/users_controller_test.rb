require 'test_helper'

class UsersControllerTest < ActionController::TestCase
  def setup
  end

  def test_forgot_password_success
    get :forgot_password
    assert_response :success
    post :forgot_password, :username => users(:usera).login
  end
end
