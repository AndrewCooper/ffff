require 'test_helper'
require 'openssl'

class UsersControllerTest < ActionController::TestCase
  def setup
    @session = { :user => users(:usera).session_info }
    @params = { :user=>users(:usera).attributes, :password=>{ :new=>"", :confirm=>"" } }
  end

  test 'should get edit' do
    get :edit, nil, @session
    assert_response :success
    assert_equal "FFFF :: Edit Profile", assigns[:title]
    assert_not_nil assigns[:item]
  end

  test 'should put update' do
    put :update, @params, @session
    assert_response :redirect
    assert_redirected_to edit_user_path
    assert_equal "Profile Successfully Updated", flash[:notice]
  end

  test 'should put update with password fail' do
    oldpass = users(:usera).password
    @params[:password][:new] = "abcdefg"
    @params[:password][:confirm] = "qwertyuiop"
    put :update, @params, @session
    assert_response :redirect
    assert_redirected_to edit_user_path
    assert_equal "Profile Successfully Updated", flash[:notice]
    assert_equal oldpass, User.find(users(:usera)).password
  end

  test 'should put update with password succeed' do
    oldpass = users(:usera).password
    @params[:password][:new] = "abcdefg"
    @params[:password][:confirm] = "abcdefg"
    put :update, @params, @session
    assert_response :redirect
    assert_redirected_to edit_user_path
    assert_equal "Profile Successfully Updated", flash[:notice]
    assert_not_equal oldpass, User.find(users(:usera)).password
  end

  test 'should get forgot_password' do
    get :forgot_password
    assert_response :success
    assert_equal "FFFF :: Forgot Password", assigns[:title]
  end

  test 'should put reset_password' do
    @params = { :username => users(:usera).login }
    assert_difference 'ActionMailer::Base.deliveries.size', +1 do
      put :reset_password, @params
    end
    u = User.find(users(:usera))
    mail = ActionMailer::Base.deliveries.first
    assert_equal u.email, mail.to[0]

    newpass = /Your new password is: (.+)\./.match( mail.body.encoded )[1]
    newdig = OpenSSL::Digest::SHA1.hexdigest(newpass)
    assert_equal newdig, u.password

    assert_equal "The email has been successfully sent.", flash[:notice]
    assert_response :redirect
    assert_redirected_to forgot_password_user_path
  end

  test 'should put reset_password with unknown_user' do
    @params = { :username => "asdfasdf" }
    assert_difference 'ActionMailer::Base.deliveries.size', 0 do
      put :reset_password, @params
    end
    assert_not_nil flash[:warning]
    assert_response :redirect
    assert_redirected_to forgot_password_user_path
  end
end
