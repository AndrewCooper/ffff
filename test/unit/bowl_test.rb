require 'test_helper'

class BowlTest < ActiveSupport::TestCase
  def setup
    @bowl = bowls(:pokesVSwildcats)
  end

  def test_bowl_exists
    assert_not_nil @bowl
    assert_kind_of Bowl,  @bowl
  end

  def test_game_exists
    assert_not_nil @bowl.game
    assert_kind_of Game, @bowl.game
  end
end
