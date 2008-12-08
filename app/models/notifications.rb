class Notifications < ActionMailer::Base

  def signup( user )
    @subject    = 'Notifications#signup'
    @body       = {:user => user}
    @recipients = user.email
    @from       = 'FFFF Administration <admin@hkcreations.org>'
    @sent_on    = Time.now
    @headers    = {}
  end

  def forgot_password( user, new_pass )
    @subject    = 'Forgotten Password for FFFF'
    @body       = {:user => user, :password => new_pass}
    @recipients = user.email
    @from       = 'FFFF Administration <admin@hkcreations.org>'
    @sent_on    = Time.now
    @headers    = {}
  end

  def picks_alert( user, games )
    @subject    = 'Pending Picks Alert'
    @body       = {:user => user, :games => games}
    @recipients = user.email
    @from       = 'FFFF Administration <admin@hkcreations.org>'
    @sent_on    = Time.now
    @headers    = {}
  end
end
