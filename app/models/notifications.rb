class Notifications < ActionMailer::Base

  def signup( user )
    @user       = user
    @subject    = 'Notifications#signup'
    @recipients = user.email
    @from       = 'FFFF Administration <admin@hkcreations.org>'
    @sent_on    = Time.now
  end

  def forgot_password( user, new_pass )
    @user       = user
    @password   = new_pass
    @subject    = 'Forgotten Password for FFFF'
    @recipients = user.email
    @from       = 'FFFF Administration <admin@hkcreations.org>'
    @sent_on    = Time.now
  end

  def picks_alert( user, games )
    @user       = user
    @games      = games
    @subject    = 'Pending Picks Alert'
    @recipients = user.email
    @from       = 'FFFF Administration <admin@hkcreations.org>'
    @sent_on    = Time.now
  end
end
