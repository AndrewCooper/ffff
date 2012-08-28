require 'test_helper'

class TeamsControllerTest < ActionController::TestCase
  test 'should get index' do
    get :index
    assert_response :success
    assert_equal "FFFF :: Browse Teams", assigns[:title]
    assert_not_nil assigns[:teams]
  end

  test 'should get show' do
    get :show, {:id=>teams(:pokes).id}
    assert_response :success
    assert_equal "FFFF :: Show Team", assigns[:title]
    assert_not_nil assigns[:team]
  end
end
