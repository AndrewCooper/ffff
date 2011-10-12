require 'test_helper'

class Admin::UsersControllerTest < ActionController::TestCase
  def setup
    @user = users(:usera)
    @session = { :user => users(:admin).session_info }
  end

  test 'should get index' do
    get :index, nil, @session
    assert_response :success
    assert_equal "Administration :: Users", assigns[:title]
    assert_not_nil assigns[:item]
    assert_not_nil assigns[:items]
  end

  test 'should post create as html' do
    b = User.new( :login=>"asdf", :password=>"asdfg" )
    post :create, {:item=>b.attributes}, @session
    assert_response :redirect
    assert_redirected_to admin_users_path
    assert_equal b.attributes, assigns[:item].attributes.except!("id")
    assert_not_nil assigns[:items]
  end

  test 'should post create as js' do
    b = User.new( :login=>"asdf", :password=>"asdfg" )
    xhr :post, :create, {:item=>b.attributes}, @session
    assert_response :success
    assert_equal b.attributes, assigns[:item].attributes.except!("id")
    assert_not_nil assigns[:items]
  end

  test 'should get new' do
    xhr :get, :new, nil, @session
    assert_response :success
    assert assigns[:item].new_record?
  end

  test 'should get edit' do
    xhr :get, :edit, {:id=>@user.id}, @session
    assert_response :success
    assert_equal @user, assigns[:item]
  end

  test 'should get show' do
    xhr :get, :show, {:id=>@user.id}, @session
    assert_response :success
    assert_equal @user, assigns[:item]
  end

  test 'should put update as html' do
    @user.firstname = "asdfasdf"
    put :update, {:id=>@user.id,:item=>@user.attributes}, @session
    assert_response :redirect
    assert_redirected_to admin_users_path
    assert_equal @user, User.find(@user.id)
  end

  test 'should put update as js' do
    @user.firstname = "asdfasdf"
    xhr :put, :update, {:id=>@user.id,:item=>@user.attributes}, @session
    assert_response :success
    assert_equal @user, User.find(@user.id)
  end

  test 'should put failed update as html' do
    @user.login = "as"
    put :update, {:id=>@user.id,:item=>@user.attributes}, @session
    assert_response :redirect
    assert_redirected_to edit_admin_user_path( @user.id )
    assert_equal @user, User.find(@user.id)
  end

  test 'should put failed update as js' do
    @user.login = "as"
    xhr :put, :update, {:id=>@user.id,:item=>@user.attributes}, @session
    assert_response :success
    assert_equal @user, User.find(@user.id)
  end

  test 'should delete as html' do
    delete :destroy, {:id=>@user.id}, @session
    assert_response :redirect
    assert_redirected_to admin_users_path
    assert_equal @user, assigns[:item]
  end

  test 'should delete as js' do
    xhr :delete, :destroy, {:id=>@user.id}, @session
    assert_response :success
    assert_equal @user, assigns[:item]
  end
end
