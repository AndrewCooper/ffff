require 'test_helper'

class LoginControllerTest < ActionController::TestCase
  test 'should get login as html' do
    get :request_login
    assert_response :redirect
  end

  test 'should get login as js' do
    get :request_login, {:format=>:js}
    assert_response :success
    assert_not_nil session[:challenge]
    assert_nil     session[:user]
  end
end
