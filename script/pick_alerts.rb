#!/usr/bin/env rails runner

class PickAlertsRunner
  def run
    games = Game.includes(:away_team,:home_team).where("games.gametime >= ? AND games.gametime < ?",Time.now,Time.now.tomorrow)
    User.all.each do |user|
      if !games.empty? then
        crit_games = []
        games.each do |game|
          if game.picks.where(:user_id=>user).empty?
            crit_games.push game
          end
        end
        if !crit_games.empty? and user.alerts? then
          Notifications.picks_alert(user,crit_games).deliver
        end
      end
    end
  end
end

if __FILE__ == $0
  x = PickAlertsRunner.new
  x.run
end
