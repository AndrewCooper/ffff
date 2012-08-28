require 'test_helper'
require Rails.root.join('script', 'pick_alerts')

class PickAlertsTest < ActiveSupport::TestCase
  test "send alerts" do
    ActionMailer::Base.deliveries.clear
    par = PickAlertsRunner.new
    assert_difference 'ActionMailer::Base.deliveries.size', +1 do
      par.run
    end
  end
end
