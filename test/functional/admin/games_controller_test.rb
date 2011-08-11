require File.dirname(__FILE__) + '/../../test_helper'
require 'admin/game_controller'

# Re-raise errors caught by the controller.
class Admin::GamesController; def rescue_action(e) raise e end; end

class Admin::GamesControllerTest < Test::Unit::TestCase
  def setup
    @controller = Admin::GamesController.new
    @request    = ActionController::TestRequest.new
    @response   = ActionController::TestResponse.new
  end

  # Replace this with your real tests.
  def test_truth
    assert true
  end
end
