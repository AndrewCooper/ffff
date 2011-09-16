require 'test_helper'

class TeamTest < ActiveSupport::TestCase
  def setup
    @team = teams(:wildcats)
    @away_game = games(:wildcatsVSjayhawks)
    @home_game = games(:pokesVSwildcats)
  end

  test 'has_many :away_games, :class_name => "Game", :foreign_key => "away_team_id"' do
    assert_not_nil @team
    assert_not_nil @team.away_games
    assert_kind_of Array, @team.away_games
    assert_equal 1, @team.away_games.all.count
    assert_equal 1, @team.away_games.all.count( @away_game )
  end

  test 'has_many :home_games, :class_name => "Game", :foreign_key => "home_team_id"' do
    assert_not_nil @team
    assert_not_nil @team.home_games
    assert_kind_of Array, @team.home_games
    assert_equal 1, @team.home_games.all.count
    assert_equal 1, @team.home_games.all.count( @home_game )
  end

  test "fullname" do
    assert_equal @team.location + " " + @team.name, @team.fullname
  end
end
