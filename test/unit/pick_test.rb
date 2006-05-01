require File.dirname(__FILE__) + '/../test_helper'

class PickTest < Test::Unit::TestCase
  fixtures :picks

  def setup
    @pick = Pick.find(1)
  end

  # Replace this with your real tests.
  def test_truth
    assert_kind_of Pick,  @pick
  end
end
