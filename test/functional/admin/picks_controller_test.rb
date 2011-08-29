require File.dirname(__FILE__) + '/../../test_helper'
require 'admin/pick_controller'

# Re-raise errors caught by the controller.
class Admin::PicksController; def rescue_action(e) raise e end; end

class Admin::PicksControllerTest < Test::Unit::TestCase
  def setup
    @controller = Admin::PicksController.new
    @request    = ActionController::TestRequest.new
    @response   = ActionController::TestResponse.new
  end

  # Replace this with your real tests.
  def test_truth
    assert true
  end
end
