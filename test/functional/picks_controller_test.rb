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
    assert_not_nil assigns(:user)
    assert_not_nil assigns(:weeks)
  end

  test 'should get edit' do
    get :edit, nil, @session
    assert_response :success
    assert_not_nil assigns(:title)
    assert_not_nil assigns(:user)
    assert_not_nil assigns(:weeks)
  end

  test 'should update picks on time' do
    p = picks(:usera_first_pick)
    p.home_score = 99
    params = { :picks => { p.id => p.attributes } }
    put :update, params, @session
    assert_redirected_to picks_path
    assert_equal "1 picks updated.", flash[:notice]
    assert_equal nil, flash[:warning]
    assert_equal p.attributes, Pick.find(p.id).attributes
  end

  test 'should update picks overdue' do
    p = picks(:usera_second_pick)
    p.home_score = 99
    params = { :picks => { p.id => p.attributes } }
    put :update, params, @session
    assert_redirected_to picks_path
    assert_equal nil, flash[:notice]
    assert_match "1 games have already started.", flash[:warning]
    assert_not_equal p.attributes, Pick.find(p.id).attributes
  end
end
