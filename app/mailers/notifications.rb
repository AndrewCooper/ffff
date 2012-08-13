class Notifications < ActionMailer::Base
  default :from => 'FFFF Administration <admin@hkcreations.org>'

  def signup( user )
    @user       = user
    @subject    = 'Notifications#signup'
    mail(:to => user.email, :subject => @subject ) 
  end

  def forgot_password( user, new_pass )
    @user       = user
    @password   = new_pass
    @subject    = 'Forgotten Password for FFFF'
    mail(:to => user.email, :subject => @subject ) 
  end

  def picks_alert( user, games )
    @user       = user
    @games      = games
    @subject    = 'Pending Picks Alert'
    mail(:to => user.email, :subject => @subject ) 
  end
end
