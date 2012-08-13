require 'test_helper'

class Admin::PicksControllerTest < ActionController::TestCase
  def setup
    @pick = picks(:usera_first_pick)
    @session = { :user => users(:admin).session_info }
  end

  test 'should get index' do
    get :index, nil, @session
    assert_response :success
    assert_equal "Administration :: Picks", assigns[:title]
    assert_not_nil assigns[:users]
    assert_not_nil assigns[:games]
  end

  test 'should get edit with user id' do
    get :edit, {:user_id=>users(:usera).id}, @session
    assert_response :success
    assert_not_nil assigns[:weeks]
    assert_not_nil assigns[:user]
    assert_equal users(:usera), assigns[:user]
  end

  test 'should get edit with game id' do
    get :edit, {:game_id=>games(:pokesVSsooners).id}, @session
    assert_response :success
    assert_not_nil assigns[:matches]
    assert_not_nil assigns[:game]
    assert_equal games(:pokesVSsooners), assigns[:game]
  end

  test 'should put update existing picks' do
    @pick.home_score = 20
    put :update, {:picks=>{@pick.id=>@pick.attributes}}, @session
    assert_response :redirect
    assert_redirected_to admin_picks_path
    assert_equal @pick, Pick.find(@pick.id)
  end

  test 'should put update new picks' do
    @pick = Pick.new( :user=>users(:usera), :game=>games(:pokesVSwildcats),
                      :home_score=>10, :away_score=>15 )
    put :update, {:newpicks=>[@pick.attributes]}, @session
    assert_response :redirect
    assert_redirected_to admin_picks_path

    np = Pick.where(:user_id=>@pick.user_id,:game_id=>@pick.game_id).first
    assert_equal @pick.attributes.except("id"),
                 np.attributes.except("id")
  end
end
