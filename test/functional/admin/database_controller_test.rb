require 'test_helper'

class Admin::DatabaseControllerTest < ActionController::TestCase
  def setup
    @session = { :user => users(:admin).session_info }
  end

  test 'should get index' do
    get :index, nil, @session
    assert_response :success
    assert_not_nil assigns[:title]
  end

  test 'should reset scores' do
    params = {"scores"=>"1"}
    post :reset, params, @session
    assert_response :redirect
    assert_redirected_to admin_database_path
    assert_equal "Tables Scores have been reset.", flash[:notice]
    assert_equal 0, Score.all.size
  end

  test 'should reset picks' do
    params = {"picks"=>"1"}
    post :reset, params, @session
    assert_response :redirect
    assert_redirected_to admin_database_path
    assert_equal "Tables Picks have been reset.", flash[:notice]
    assert_equal 0, Pick.all.size
  end

  test 'should reset bowls' do
    params = {"bowls"=>"1"}
    post :reset, params, @session
    assert_response :redirect
    assert_redirected_to admin_database_path
    assert_equal "Tables Bowls have been reset.", flash[:notice]
    assert_equal 0, Bowl.all.size
  end

  test 'should reset games' do
    params = {"games"=>"1"}
    post :reset, params, @session
    assert_response :redirect
    assert_redirected_to admin_database_path
    assert_equal "Tables Games have been reset.", flash[:notice]
    assert_equal 0, Game.all.size
  end

  test 'should reset teams' do
    params = {"teams"=>"1"}
    post :reset, params, @session
    assert_response :redirect
    assert_redirected_to admin_database_path
    assert_equal "Tables Teams have been reset.", flash[:notice]
    assert_equal 0, Team.all.size
  end

  test 'should reset users' do
    params = {"users"=>"1"}
    post :reset, params, @session
    assert_response :redirect
    assert_redirected_to admin_database_path
    assert_equal "Tables Users have been reset.", flash[:notice]
    assert_equal 0, User.all.size
  end

  test 'should reset all' do
    params = {"scores"=>"1", "picks"=>"1", "bowls"=>"1", "games"=>"1", "teams"=>"1", "users"=>"1"}
    post :reset, params, @session
    assert_response :redirect
    assert_redirected_to admin_database_path
    assert_equal "Tables Scores, Picks, Bowls, Games, Teams, and Users have been reset.", flash[:notice]
    assert_equal 0, Score.all.size
    assert_equal 0, Pick.all.size
    assert_equal 0, Game.all.size
    assert_equal 0, Team.all.size
    assert_equal 0, User.all.size
  end
end
