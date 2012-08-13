require 'test_helper'

class Admin::BowlsControllerTest < ActionController::TestCase
  def setup
    @bowl = bowls(:pokesVSwildcats)
    @session = { :user => users(:admin).session_info }
  end

  test 'should get index' do
    get :index, nil, @session
    assert_response :success
    assert_equal "Administration :: Bowls", assigns[:title]
    assert_not_nil assigns[:item]
    assert_not_nil assigns[:items]
    assert_not_nil assigns[:games]
  end

  test 'should post create as html' do
    b = Bowl.new( :name=>"asdf", :location=>"qwer", :multiplier=>5, :url=>"asldkjf", :game_id=>games(:wildcatsVSjayhawks).id )
    post :create, {:item=>b.attributes}, @session
    assert_response :redirect
    assert_redirected_to admin_bowls_path
    assert_equal b.attributes.except!("id"), assigns[:item].attributes.except!("id")
    assert_not_nil assigns[:items]
  end

  test 'should post create as js' do
    b = Bowl.new( :name=>"asdf", :location=>"qwer", :multiplier=>5, :url=>"asldkjf", :game_id=>games(:wildcatsVSjayhawks).id )
    xhr :post, :create, {:item=>b.attributes}, @session
    assert_response :success
    assert_equal b.attributes.except!("id"), assigns[:item].attributes.except!("id")
    assert_not_nil assigns[:items]
  end

  test 'should get new' do
    xhr :get, :new, nil, @session
    assert_response :success
    assert assigns[:item].new_record?
    assert_not_nil assigns[:games]
  end

  test 'should get edit' do
    xhr :get, :edit, {:id=>@bowl.id}, @session
    assert_response :success
    assert_equal @bowl, assigns[:item]
    assert_not_nil assigns[:games]
  end

  test 'should get show' do
    xhr :get, :show, {:id=>@bowl.id}, @session
    assert_response :success
    assert_equal @bowl, assigns[:item]
  end

  test 'should put update as html' do
    @bowl.name = "asdfasdf"
    put :update, {:id=>@bowl.id,:item=>@bowl.attributes}, @session
    assert_response :redirect
    assert_redirected_to admin_bowls_path
    assert_equal @bowl, Bowl.find(@bowl.id)
  end

  test 'should put update as js' do
    @bowl.name = "asdfasdf"
    xhr :put, :update, {:id=>@bowl.id,:item=>@bowl.attributes}, @session
    assert_response :success
    assert_equal @bowl, Bowl.find(@bowl.id)
  end

  test 'should put failed update as html' do
    @bowl.multiplier = -5
    put :update, {:id=>@bowl.id,:item=>@bowl.attributes}, @session
    assert_response :redirect
    assert_redirected_to edit_admin_bowl_path( @bowl.id )
    assert_not_equal @bowl.attributes, Bowl.find(@bowl.id).attributes
  end

  test 'should put failed update as js' do
    @bowl.multiplier = -5
    xhr :put, :update, {:id=>@bowl.id,:item=>@bowl.attributes}, @session
    assert_response :success
    assert_not_equal @bowl.attributes, Bowl.find(@bowl.id).attributes
  end

  test 'should delete as html' do
    delete :destroy, {:id=>@bowl.id}, @session
    assert_response :redirect
    assert_redirected_to admin_bowls_path
    assert_equal @bowl, assigns[:item]
  end

  test 'should delete as js' do
    xhr :delete, :destroy, {:id=>@bowl.id}, @session
    assert_response :success
    assert_equal @bowl, assigns[:item]
  end
end
