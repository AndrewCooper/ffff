require 'test_helper'

class ScoreTest < ActiveSupport::TestCase
  def test_exists
    score = Score.first
    assert_kind_of Score, score
  end
end
