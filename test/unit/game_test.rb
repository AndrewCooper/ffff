require 'test_helper'

class GameTest < ActiveSupport::TestCase
  def test_game_exists
    game = games(:pokesVSsooners)
    assert_not_nil game
    assert_kind_of Game, game
  end

  def test_bowl_exists
    game = games(:pokesVSwildcats)
    assert_not_nil game
    assert_not_nil game.bowl
    assert_kind_of Bowl, game.bowl
  end
end
