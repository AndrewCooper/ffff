
require 'test_helper'

class PickTest < ActiveSupport::TestCase
	
  def test_relationships
    #belongs_to :user
    assert_equal( users(:usera).id, picks(:usera_first_pick).user_id )
    assert_equal( users(:usera).id, picks(:usera_first_pick).user.id )

    #belongs_to :game
    assert_equal( games(:pokesVSsooners).id, picks(:usera_first_pick).game_id )
    assert_equal( games(:pokesVSsooners).id, picks(:usera_first_pick).game.id )
  end
	
  def test_validations
    # validates_numericality_of :home_score, :integer_only=>true, :greater_than_or_equal_to=>0
    # validates_numericality_of :away_score, :integer_only=>true, :greater_than_or_equal_to=>0
    # validates_numericality_of :pick_score, :integer_only=>true, :greater_than_or_equal_to=>0

    p = picks( :usera_first_pick )
    p["home_score"] = -1           # Negative integer
    assert_invalid( p )
    p["home_score"] = 50           # Positive integer
    assert_valid( p )

    p["away_score"] = -1           # Negative integer
    assert_invalid( p )
    p["away_score"] = 50           # Positive integer
    assert_valid( p )

    p["pick_score"] = -1           # Negative integer
    assert_invalid( p )
    p["pick_score"] = 50           # Positive integer
    assert_valid( p )
  end
	
  def test_pick_alerts
    Pick.email_alerts
    assert_equal( 1, ActionMailer::Base.deliveries.length )
  end
	
end