require 'test_helper'

class UserTest < ActiveSupport::TestCase
  def test_empty_password
    oldpass = users(:usera).password.clone
    users(:usera).password = ""
    users(:usera).save
    assert_equal 0, users(:usera).errors.count

    user = User.find(users(:usera).id)
    assert_equal oldpass, user.password
  end

  def test_name
    u = users(:usera)
    assert_equal( "#{u.firstname} #{u.lastname}", u.name )
  end

  def test_session_info
    u = users(:usera)
    session_hash = { :name=>"#{u.firstname} #{u.lastname}", :uid=>u.id, :admin=>u.is_admin }
    assert_equal( session_hash, u.session_info )
  end

  def test_relationships_picks
    # has_many :picks, :dependent => :delete_all
    u = users(:usera)
    assert_equal( 2, u.picks.count )
    pick_ids = u.picks.collect{ |p| p.user_id }
    assert_equal( [u.id]*2, pick_ids )
    assert_equal( [games(:pokesVSsooners).id, games(:wildcatsVSjayhawks).id].sort,
      u.picks.collect { |p| p.game_id }.sort )
    User.destroy(u)
    assert_raise ActiveRecord::RecordNotFound do
      Pick.find(pick_ids)
    end
  end

  def test_relationship_scores
    # has_many :scores, :dependent => :delete_all
    u = users(:usera)
    assert_equal( 2, u.scores.count )
    score_ids = u.scores.collect{ |s| s.user_id }
    assert_equal( [u.id]*2, score_ids )
    assert_equal( [scores(:usera_score1).week, scores(:usera_score2).week].sort,
      u.scores.collect { |s| s.week }.sort )
    User.destroy(u)
    assert_raise ActiveRecord::RecordNotFound do
      Score.find( score_ids )
    end
  end

  def test_validation_login
    user = users(:usera).clone
    assert(!user.valid?)
    assert(user.errors[:login].any?, "Expected an error after validation")

    user.login = "12"
    user.save
    assert_equal 1, user.errors.count

    user.login = "l"*41
    user.save
    assert_equal 1, user.errors.count
  end

  def test_validation_presence
    user = User.new
    user.password = '1'
    user.save
    assert_equal 3, user.errors.count, user.errors.to_xml

    user.login = "abc"
    user.password = '1';
    user.save
    assert_equal 1, user.errors.count, user.errors.to_xml

    user.password = "12345"
    user.save
    assert_equal 0, user.errors.count, user.errors.to_xml
  end
end
