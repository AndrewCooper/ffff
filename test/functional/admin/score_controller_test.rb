require 'test_helper'

class Admin::ScoreControllerTest < ActionController::TestCase
  def setup
    @session = { :user=>users(:admin).session_info }
  end

  test 'should get index' do
    get :index, nil, @session
    assert_response :success
    assert_equal "Administration :: Scores", assigns[:title]
  end

  test 'should post calculate' do
    post :calculate, nil, @session
    assert_response :success
    assert_equal "Administration :: Calculation Results", assigns[:title]
    assert_not_nil assigns[:scores]
  end
end
