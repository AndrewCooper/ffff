require 'test_helper'

class FakeController < ApplicationController
  def index
    flash[:notice] = "OK"
    render :text => "OK"
  end
end

class FakeControllerTest < ActionController::TestCase
  test 'should get index when authorized' do
    with_routing do |set|
      fake_routes(set)
      @session = { :user => users(:usera).session_info }
      get :index, nil, @session
      assert_response :success
      assert_equal "OK", flash[:notice]
      assert_equal "OK", @response.body
    end
  end

  test 'should redirect to root when unauthorized' do
    with_routing do |set|
      fake_routes(set)
      get :index
      assert_response :redirect
      assert_redirected_to root_path
      assert_equal "Login Required.", flash[:notice]
    end
  end

  private
  def fake_routes( set )
    set.draw do
      get "fake" => "fake#index"
      root :to => "fake#root"
    end
  end
end
