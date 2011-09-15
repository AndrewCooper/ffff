require 'test_helper'

class ScoreTest < ActiveSupport::TestCase
  def setup
    @user = users(:usera)
    @score = scores(:usera_score1)
  end

  test "belongs_to :user" do
    assert_not_nil @score.user
    assert_kind_of User, @score.user
    assert_equal @user, @score.user
  end

  test "user_stats" do
    stats = Score.user_stats( users(:usera).id )
    assert_equal 2, stats[:rank]
    assert_equal 8, stats[:score]
    assert_equal 0, stats[:ties].count
  end
end
