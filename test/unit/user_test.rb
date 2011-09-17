require 'test_helper'

class UserTest < ActiveSupport::TestCase
  def setup
    @user = users(:usera)
  end

  test 'empty password does not modify saved password' do
    oldpass = @user.password.clone
    @user.password = ""
    @user.save
    assert_equal 0, users(:usera).errors.count

    user = User.find(@user.id)
    assert_equal oldpass, user.password
  end

  test 'class method ranked' do
    @scores = User.ranked.all
    assert_equal users(:userb), @scores.first
    assert_equal users(:usera), @scores.second
    assert_equal 9, @scores.second.wins
    assert_equal 4, @scores.second.closests
    assert_equal 1, @scores.second.sevens
    assert_equal 1, @scores.second.perfects
    assert_equal 8, @scores.second.total
    assert_equal users(:userc), @scores.third
  end

  test 'instance method name' do
    assert_equal @user.firstname + " " + @user.lastname, @user.name
  end

  test 'instance method session_info' do
    session_hash = { :name=>"#{@user.firstname} #{@user.lastname}", :uid=>@user.id, :admin=>@user.is_admin }
    assert_equal session_hash, @user.session_info
  end

  test 'has_many :picks, :dependent => :delete_all' do
    assert_equal 2, @user.picks.count
    pick_ids = @user.picks.collect{ |p| p.user_id }
    assert_equal [@user.id]*2, pick_ids
    assert_equal [games(:pokesVSsooners).id, games(:wildcatsVSjayhawks).id].sort, @user.picks.collect { |p| p.game_id }.sort
    User.destroy(@user)
    assert_raise ActiveRecord::RecordNotFound do
      Pick.find(pick_ids)
    end
  end

  test 'has_many :scores, :dependent => :delete_all' do
    assert_equal 2, @user.scores.count
    score_ids = @user.scores.collect{ |s| s.user_id }
    assert_equal [@user.id]*2, score_ids
    assert_equal [scores(:usera_score1).week, scores(:usera_score2).week].sort, @user.scores.collect { |s| s.week }.sort
    User.destroy(@user)
    assert_raise ActiveRecord::RecordNotFound do
      Score.find( score_ids )
    end
  end

  test 'validates_uniqueness_of :login, :message => "NOT A UNIQUE LOGIN"' do
    userclone = users(:usera).clone
    assert !userclone.valid?
    assert userclone.errors[:login].any?
  end

  test 'validates_length_of :login, :within => 3..40' do
    @user.login = "l"*2
    assert !@user.valid?

    @user.login = "l"*41
    assert !@user.valid?

    @user.login = "l"*3
    assert @user.valid?

    @user.login = "l"*40
    assert @user.valid?
  end

  test 'validates_length_of :password, :within => 5..40' do
    @user.password = "p"*4
    assert !@user.valid?

    @user.password = "p"*41
    assert !@user.valid?

    @user.password = "p"*5
    assert @user.valid?

    @user.password = "p"*40
    assert @user.valid?
  end

  test 'validates_presence_of :login, :password' do
    @user = User.new
    @user.password = "p"*5
    assert !@user.valid?

    @user.login = "l"*3
    assert @user.valid?
  end
end
