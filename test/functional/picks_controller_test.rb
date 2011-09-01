require File.dirname(__FILE__) + '/../test_helper'
require 'pick_controller'

# Re-raise errors caught by the controller.
class PicksController; def rescue_action(e) raise e end; end

class PicksControllerTest < Test::Unit::TestCase
  def setup
    @controller = PicksController.new
    @request    = ActionController::TestRequest.new
    @response   = ActionController::TestResponse.new
  end

  # Replace this with your real tests.
  def test_truth
    assert true
  end

  def test_pick_alerts
    Pick.email_alerts
    assert_equal( 1, ActionMailer::Base.deliveries.length )
  end
end
