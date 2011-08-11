class Pick < ActiveRecord::Base
  belongs_to :user
  belongs_to :game

  validates_numericality_of :home_score, :integer_only=>true, :greater_than_or_equal_to=>0
  validates_numericality_of :away_score, :integer_only=>true, :greater_than_or_equal_to=>0
  validates_numericality_of :pick_score, :integer_only=>true, :greater_than_or_equal_to=>0, :allow_nil=>true

  def calculate_score(gh,ga)
    (home_score - gh).abs + (away_score - ga).abs + ((home_score - away_score).abs - (gh - ga).abs).abs
  end

  def to_s
    "{uid=>#{user_id}, gid=>#{game_id}, hs=>#{home_score}, as=>#{away_score}, ps=>#{pick_score}}"
  end

  def self.email_alerts
    users = User.find(:all)
    mails = []
    users.each do |user|
      games = Game.upcoming_games_with_picks(user.id,Time.now)
      if !games.empty? then
        crit_games = []
        games.each do |game|
          debugger
          if game.gametime < Time.now.tomorrow and game.pid.nil? then
            crit_games.push game
          end
        end
        debugger
        if !crit_games.empty? and user.alerts? then
          Notifications.deliver_picks_alert(user,crit_games)
        end
      end
    end
    # if !mails.empty? then
    #   puts "#{mails.length} mails to be sent"
    #   options = ActionMailer::Base.server_settings
    #   exceptions = {}
    #   mails.each_slice(25) do |mails_slice|
    #     Net::SMTP.start(options[:address],options[:port],options[:domain],options[:user_name],options[:password],options[:authentication]) do |sender|
    #       mails_slice.each do |mail|
    #         begin
    #           puts "Sending alert to #{mail.to}"
    #           sender.sendmail mail.encoded, mail.from, mail.to
    #         rescue Exception => e
    #           exceptions[recipient] = e
    #           #needed as the next mail will send command MAIL FROM, which would
    #           #raise a 503 error: "Sender already given"
    #           sender.finish
    #           sender.start
    #         end
    #       end
    #     end
    #   end
    # end
  end
end
