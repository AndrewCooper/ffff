require 'test_helper'

class GameTest < ActiveSupport::TestCase
  def setup
    @game = games(:pokesVSsooners)
  end

  test "game exists" do
    assert_not_nil @game
    assert_kind_of Game, @game
  end

  test "has_one :bowl" do
    @game = games(:pokesVSwildcats)
    assert_not_nil @game
    assert_not_nil @game.bowl
    assert_kind_of Bowl, @game.bowl
    assert_equal bowls(:pokesVSwildcats), @game.bowl
  end

  test "belongs_to :away_team" do
    assert_not_nil @game
    assert_not_nil @game.away_team
    assert_kind_of Team, @game.away_team
    assert_equal teams(:pokes), @game.away_team
  end

  test "belongs_to :home_team" do
    assert_not_nil @game
    assert_not_nil @game.away_team
    assert_kind_of Team, @game.away_team
    assert_equal teams(:pokes), @game.away_team
  end

  test "has_many :picks, :dependent=>:delete_all" do
    assert_not_nil @game
    assert_not_nil @game.picks
    assert_kind_of Array, @game.picks
    assert_equal 2, @game.picks.all.count
    assert_equal 1, @game.picks.all.count( picks(:usera_first_pick) )

    game_id = @game.id
    @game.destroy
    test_picks = Pick.where( :game_id => game_id )
    assert_equal 0, test_picks.count
  end

  test "fullname" do
    at = teams(:pokes)
    ht = teams(:sooners)
    assert_equal at.fullname + " @ " + ht.fullname, @game.fullname
  end
end
