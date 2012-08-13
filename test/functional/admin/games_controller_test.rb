require 'test_helper'

class Admin::GamesControllerTest < ActionController::TestCase
  def setup
    @game = games(:pokesVSwildcats)
    @session = { :user => users(:admin).session_info }
  end

  test 'should get index' do
    get :index, nil, @session
    assert_response :success
    assert_equal "Administration :: Games", assigns[:title]
    assert_not_nil assigns[:item]
    assert_not_nil assigns[:items]
    assert_not_nil assigns[:teams]
  end

  test 'should post create as html' do
    b = Game.new( :home_team=>teams(:sooners), :away_team=>teams(:jayhawks),
                  :home_score=>10, :away_score=>14,
                  :is_bowl=>0, :week=>4, :gametime=>Time.parse("2012-08-13 18:00:00") )
    post :create, {:item=>b.attributes}, @session
    assert_response :redirect
    assert_redirected_to admin_games_path
    assert_equal b.attributes.except("id"), assigns[:item].attributes.except("id")
    assert_not_nil assigns[:items]
  end

  test 'should post create as js' do
    b = Game.new( :home_team=>teams(:sooners), :away_team=>teams(:jayhawks),
                  :home_score=>10, :away_score=>14,
                  :is_bowl=>0, :week=>4, :gametime=>Time.parse("2012-08-13 18:00:00") )
    xhr :post, :create, {:item=>b.attributes}, @session
    assert_response :success
    assert_equal b.attributes.except("id"), assigns[:item].attributes.except("id")
    assert_not_nil assigns[:items]
  end

  test 'should get new' do
    xhr :get, :new, nil, @session
    assert_response :success
    assert assigns[:item].new_record?
    assert_not_nil assigns[:teams]
  end

  test 'should get edit' do
    xhr :get, :edit, {:id=>@game.id}, @session
    assert_response :success
    assert_equal @game, assigns[:item]
    assert_not_nil assigns[:teams]
  end

  test 'should get show' do
    xhr :get, :show, {:id=>@game.id}, @session
    assert_response :success
    assert_equal @game, assigns[:item]
  end

  test 'should put update as html' do
    @game.home_score = 20
    put :update, {:id=>@game.id,:item=>@game.attributes}, @session
    assert_response :redirect
    assert_redirected_to admin_games_path
    assert_equal @game, Game.find(@game.id)
  end

  test 'should put update as js' do
    @game.home_score = 20
    xhr :put, :update, {:id=>@game.id,:item=>@game.attributes}, @session
    assert_response :success
    assert_equal @game, Game.find(@game.id)
  end

  test 'should put failed update as html' do
    @game.home_score = -5
    put :update, {:id=>@game.id,:item=>@game.attributes}, @session
    assert_response :redirect
    assert_redirected_to edit_admin_game_path( @game.id)
    assert_not_equal @game.attributes, Game.find(@game.id).attributes
  end

  test 'should put failed update as js' do
    @game.home_score = -5
    xhr :put, :update, {:id=>@game.id,:item=>@game.attributes}, @session
    assert_response :success
    assert_not_equal @game.attributes, Game.find(@game.id).attributes
  end

  test 'should delete as html' do
    delete :destroy, {:id=>@game.id}, @session
    assert_response :redirect
    assert_redirected_to admin_games_path
    assert_equal @game, assigns[:item]
  end

  test 'should delete as js' do
    xhr :delete, :destroy, {:id=>@game.id}, @session
    assert_response :success
    assert_equal @game, assigns[:item]
  end
end
