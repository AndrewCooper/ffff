require 'test_helper'

class NotificationsTest < ActionMailer::TestCase

  def setup
    @user = User.first
    @newpass = "AsDfGhJkL"
    @games = [ Game.first ]
    @from = 'admin@hkcreations.org'
  end

  test "signup" do
    mail = Notifications.signup( @user )
    assert_equal "Notifications#signup", mail.subject
    assert_equal [@user.email], mail.to
    assert_equal [@from], mail.from
    assert_match "Notifications#signup", mail.body.encoded
  end

  test "forgot_password" do
    mail = Notifications.forgot_password( @user, @newpass )
    assert_equal "Forgotten Password for FFFF", mail.subject
    assert_equal [@user.email], mail.to
    assert_equal [@from], mail.from
    assert_match "Hi #{@user.firstname}", mail.body.encoded
    assert_match "Your new password is: #{@newpass}", mail.body.encoded
  end

  test "picks_alert" do
    mail = Notifications.picks_alert( @user, @games )
    assert_equal "Pending Picks Alert", mail.subject
    assert_equal [@user.email], mail.to
    assert_equal [@from], mail.from
    assert_match "Hi #{@user.firstname}", mail.body.encoded
    assert_match "#{@games[0].gametime.strftime("%I:%M%p")} : #{@games[0].fullname}", mail.body.encoded
  end
end
