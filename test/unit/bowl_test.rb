require File.dirname(__FILE__) + '/../test_helper'

class BowlTest < Test::Unit::TestCase
  fixtures :bowls

  def setup
    @bowl = Bowl.find(1)
  end

  # Replace this with your real tests.
  def test_truth
    assert_kind_of Bowl,  @bowl
  end
end
