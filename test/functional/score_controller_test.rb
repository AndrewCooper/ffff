require File.dirname(__FILE__) + '/../test_helper'
require 'score_controller'

# Re-raise errors caught by the controller.
class ScoreController; def rescue_action(e) raise e end; end

class ScoreControllerTest < Test::Unit::TestCase
  def setup
    @controller = ScoreController.new
    @request    = ActionController::TestRequest.new
    @response   = ActionController::TestResponse.new
  end

  # Replace this with your real tests.
  def test_truth
    assert true
  end
end
