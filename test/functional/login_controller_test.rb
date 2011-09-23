require 'test_helper'

class LoginControllerTest < ActionController::TestCase
  include LoginResponseCalculation

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

  test 'login usera successfully, change password' do
    get :request_login, {:format=>:js}
    user = users(:usera)
    params = {:login => user.login, :response => calculate_login_response( user.password, session[:challenge] ) }
    post :login, params
    assert_response :redirect
    assert_redirected_to edit_user_path
    assert_equal "You are now encouraged to change your password.", flash[:notice]
  end

  test 'login userb successfully, don\'t change password' do
    get :request_login, {:format=>:js}
    user = users(:userb)
    params = {:login => user.login, :response => calculate_login_response( user.password, session[:challenge] ) }
    post :login, params
    assert_response :redirect
    assert_redirected_to root_path
    assert_equal nil, flash[:notice]
  end

  test 'login usera failure' do
    get :request_login, {:format=>:js}
    user = users(:usera)
    params = {:login => user.login, :response => "asdf" }
    post :login, params
    assert_response :redirect
    assert_redirected_to root_path
    assert_match /Password incorrect for/, flash[:notice]
    assert_equal nil, session[:user]
  end
end
