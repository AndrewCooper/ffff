require 'test_helper'

class TeamTest < ActiveSupport::TestCase
  def test_exists
    team = Team.first
    assert_kind_of Team, team
  end
end
