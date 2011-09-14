require 'test_helper'

class PickTest < ActiveSupport::TestCase
  def setup
    @pick = picks(:usera_first_pick)
    @user = users(:usera)
    @game = games(:pokesVSsooners)
  end

  test "belongs_to :user" do
    assert_not_nil @pick.user
    assert_kind_of User, @pick.user
    assert_equal @user, @pick.user
  end

  test "belongs_to :game" do
    assert_not_nil @pick.game
    assert_kind_of Game, @pick.game
    assert_equal @game, @pick.game
  end

  test "validates_numericality_of :home_score, :integer_only=>true, :greater_than_or_equal_to=>0" do
    p = Pick.new
    p.home_score = -1           # Negative integer
    assert( !p.valid? )
    p.home_score = 50           # Positive integer
    assert( p.valid? )
  end

  test "validates_numericality_of :away_score, :integer_only=>true, :greater_than_or_equal_to=>0" do
    p = Pick.new
    p.away_score = -1           # Negative integer
    assert( !p.valid? )
    p.away_score = 50           # Positive integer
    assert( p.valid? )
  end

  test "validates_numericality_of :pick_score, :integer_only=>true, :greater_than_or_equal_to=>0, :allow_nil=>true" do
    p = Pick.new
    p.pick_score = -1           # Negative integer
    assert( !p.valid? )
    p.pick_score = nil          # nil
    assert( p.valid? )
    p.pick_score = 50           # Positive integer
    assert( p.valid? )
  end

  test "calculate_score" do
    p = Pick.new( :home_score=>10, :away_score=>20 )
    assert_equal  0, p.calculate_score( 10, 20 )      # perfect pick
    assert_equal 10, p.calculate_score(  5, 20 )      # away_score >
    assert_equal 10, p.calculate_score( 15, 20 )      # away_score <
    assert_equal 10, p.calculate_score( 10, 25 )      # home_score >
    assert_equal 10, p.calculate_score( 10, 15 )      # home_score <
    assert_equal 10, p.calculate_score( 10, 15 )      # spread >
    assert_equal 10, p.calculate_score( 10, 25 )      # spread <
  end
end
