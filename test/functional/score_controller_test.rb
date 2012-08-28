require 'test_helper'

class ScoreControllerTest < ActionController::TestCase
  def setup
    @session = { :user => users(:usera).session_info }
  end

  test 'should get rankings' do
    get :rankings
    assert_response :success
    assert_equal "FFFF :: Home", assigns[:title]
    assert_not_nil assigns[:scores]
  end

  test 'should get index' do
    get :index, nil, @session
    assert_response :success
    assert_equal "FFFF :: Weekly Scores", assigns[:title]
    assert_not_nil assigns[:weeks]
    assert_not_nil assigns[:users]
  end
end
