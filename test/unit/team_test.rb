require File.dirname(__FILE__) + '/../test_helper'

class TeamTest < Test::Unit::TestCase
  fixtures :teams

  def setup
    @team = Team.find(1)
  end

  # Replace this with your real tests.
  def test_truth
    assert_kind_of User,  @team
  end
end
