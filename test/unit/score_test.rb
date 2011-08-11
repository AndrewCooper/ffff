require File.dirname(__FILE__) + '/../test_helper'

class ScoreTest < Test::Unit::TestCase
  fixtures :scores

  def setup
    @score = Score.find(1)
  end

  # Replace this with your real tests.
  def test_truth
    assert_kind_of Score,  @score
  end
end
