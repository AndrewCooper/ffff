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

  test 'validates :rankAP, :numericality => { :only_integer => true, :greater_than_or_equal_to => 0 }' do
    assert @team.valid?

    @team.rankAP = -1
    assert @team.invalid?

    @team.rankAP = 0
    assert @team.valid?

    @team.rankAP = 500
    assert @team.valid?
  end

  test 'validates :rankUSA, :numericality => { :only_integer => true, :greater_than_or_equal_to => 0 }' do
    assert @team.valid?

    @team.rankUSA = -1
    assert @team.invalid?

    @team.rankUSA = 0
    assert @team.valid?

    @team.rankUSA = 500
    assert @team.valid?
  end

  test 'validates :espnid, :numericality => { :only_integer => true, :greater_than_or_equal_to => 0 }' do
    assert @team.valid?

    @team.espnid = -1
    assert @team.invalid?

    @team.espnid = 0
    assert @team.valid?

    @team.espnid = 500
    assert @team.valid?
  end

  test "fullname" do
    assert_equal @team.location + " " + @team.name, @team.fullname
  end
end
