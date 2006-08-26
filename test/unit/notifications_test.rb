require File.dirname(__FILE__) + '/../test_helper'
require 'notifications'

class NotificationsTest < Test::Unit::TestCase
  FIXTURES_PATH = File.dirname(__FILE__) + '/../fixtures'
  CHARSET = "utf-8"

  include ActionMailer::Quoting

  def setup
    ActionMailer::Base.delivery_method = :test
    ActionMailer::Base.perform_deliveries = true
    ActionMailer::Base.deliveries = []

    @expected = TMail::Mail.new
    @expected.set_content_type "text", "plain", { "charset" => CHARSET }
  end

  def test_signup
    @expected.subject = 'Notifications#signup'
    @expected.body    = read_fixture('signup')
    @expected.date    = Time.now

    assert_equal @expected.encoded, Notifications.create_signup(@expected.date).encoded
  end

  def test_forgot_password
    @expected.subject = 'Notifications#forgot_password'
    @expected.body    = read_fixture('forgot_password')
    @expected.date    = Time.now

    assert_equal @expected.encoded, Notifications.create_forgot_password(@expected.date).encoded
  end

  def test_picks_alert
    @expected.subject = 'Notifications#picks_alert'
    @expected.body    = read_fixture('picks_alert')
    @expected.date    = Time.now

    assert_equal @expected.encoded, Notifications.create_picks_alert(@expected.date).encoded
  end

  private
    def read_fixture(action)
      IO.readlines("#{FIXTURES_PATH}/notifications/#{action}")
    end

    def encode(subject)
      quoted_printable(subject, CHARSET)
    end
end
