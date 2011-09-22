require 'test_helper'

class PicksControllerTest < ActionController::TestCase
  def setup
    @user = users(:usera)
    @session = { :user => @user.session_info }
  end

  # Replace this with your real tests.
  test 'should get index' do
    get :index, nil, @session
    assert_response :success
    assert_not_nil assigns(:title)
    assert_not_nil assigns(:tz)
    assert_not_nil assigns(:user)
    assert_not_nil assigns(:weeks)
  end

  test 'should get edit' do
    get :edit, nil, @session
    assert_response :success
    assert_not_nil assigns(:title)
    assert_not_nil assigns(:tz)
    assert_not_nil assigns(:user)
    assert_not_nil assigns(:weeks)
  end

  test 'should update picks' do
    params = { :picks => {} }
    put :update, params, @session
    assert_redirected_to picks_path
    assert_equal nil, flash[:notice]
  end
end
