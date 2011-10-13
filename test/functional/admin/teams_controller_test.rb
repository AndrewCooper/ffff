require 'test_helper'

class Admin::TeamsControllerTest < ActionController::TestCase
  def setup
    @team = teams(:pokes)
    @session = { :user => users(:admin).session_info }
  end

  test 'should get index' do
    get :index, nil, @session
    assert_response :success
    assert_equal "Administration :: Teams", assigns[:title]
    assert_not_nil assigns[:item]
    assert_not_nil assigns[:items]
  end

  test 'should post create as html' do
    t = Team.new( :name=>"asdf" )
    post :create, {:item=>t.attributes}, @session
    assert_response :redirect
    assert_redirected_to admin_teams_path
    assert_equal t.attributes, assigns[:item].attributes.except!("id")
    assert_not_nil assigns[:items]
  end

  test 'should post create as js' do
    t = Team.new( :name=>"asdf" )
    xhr :post, :create, {:item=>t.attributes}, @session
    assert_response :success
    assert_equal t.attributes, assigns[:item].attributes.except!("id")
    assert_not_nil assigns[:items]
  end

  test 'should get new' do
    xhr :get, :new, nil, @session
    assert_response :success
    assert assigns[:item].new_record?
  end

  test 'should get edit' do
    xhr :get, :edit, {:id=>@team.id}, @session
    assert_response :success
    assert_equal @team, assigns[:item]
  end

  test 'should get show' do
    xhr :get, :show, {:id=>@team.id}, @session
    assert_response :success
    assert_equal @team, assigns[:item]
  end

  test 'should put update as html' do
    @team.name = "asdfasdf"
    put :update, {:id=>@team.id,:item=>@team.attributes}, @session
    assert_response :redirect
    assert_redirected_to admin_teams_path
    assert_equal @team, Team.find(@team.id)
  end

  test 'should put update as js' do
    @team.name = "asdfasdf"
    xhr :put, :update, {:id=>@team.id,:item=>@team.attributes}, @session
    assert_response :success
    assert_equal @team, Team.find(@team.id)
  end

  test 'should put failed update as html' do
    @team.rankAP = -1
    put :update, {:id=>@team.id,:item=>@team.attributes}, @session
    assert_response :redirect
    assert_redirected_to edit_admin_team_path(@team.id)
    assert_equal @team, Team.find(@team.id)
  end

  test 'should put failed update as js' do
    @team.rankAP = -1
    xhr :put, :update, {:id=>@team.id,:item=>@team.attributes}, @session
    assert_response :success
    assert_equal @team, Team.find(@team.id)
  end

  test 'should delete as html' do
    delete :destroy, {:id=>@team.id}, @session
    assert_response :redirect
    assert_redirected_to admin_teams_path
    assert_equal @team, assigns[:item]
  end

  test 'should delete as js' do
    xhr :delete, :destroy, {:id=>@team.id}, @session
    assert_response :success
    assert_equal @team, assigns[:item]
  end
end
