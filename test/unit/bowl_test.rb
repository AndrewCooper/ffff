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

  # Cannot actually test non-integer values for multiplier, as Ruby
  # auto-converts to Integer at assignment
  test 'validates :multiplier, :numericality => { :only_integer => true, :greater_than => 0 }' do
    @bowl.multiplier = -5
    assert !@bowl.valid?

    @bowl.multiplier = 99
    assert @bowl.valid?
  end
end
