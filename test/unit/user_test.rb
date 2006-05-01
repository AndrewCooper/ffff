require File.dirname(__FILE__) + '/../test_helper'
require 'digest/sha1'

class UserTest < Test::Unit::TestCase
  fixtures :users

  def setup
    @user = User.find(1)
  end

  def test_no_password_update
    @example_user.password = ""
    @example_user.save
    assert_equal 0, @example_user.errors.count, "poop"
    @user = User.find(@example_user.id)
    assert_equal @user.password, @example_user.password
  end
  
end
