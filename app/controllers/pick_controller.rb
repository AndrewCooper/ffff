class PickController < ApplicationController

  def make
    @title = "FFFF :: Make Picks"
    @now = current_app_time
    @tz = time_zone
    if request.get?
      games = Game.upcoming_games_with_picks(session[:user][:uid],@now)
      @weeks = []
      if games.size == 0
        flash.now[:notice] = "There are no games for you to pick right now."
      else
        games.each do |g|
          if @weeks[game.week].nil?
            @weeks[game.week] = [g]
          else
            @weeks[game.week] << g
          end
        end
      end
    else
      picks = params["picks"]
      picks.each do |key,pick|
        game = Game.find(pick["game_id"])
        if @now < game.gametime
          if pick["pick_id"] == ""
            dbpick = Pick.new
            dbpick.away_score = pick["away_score"] == "" ? 0 : pick["away_score"]
            dbpick.home_score = pick["home_score"] == "" ? 0 : pick["home_score"]
            dbpick.game_id = pick["game_id"]
            dbpick.user_id = session[:user][:uid]
            dbpick.save
          else
            dbpick = Pick.find(pick["pick_id"].to_i)
            dbpick.home_score = pick["home_score"].to_i
            dbpick.away_score = pick["away_score"].to_i
            dbpick.save
          end
        else
          flash[:warning] = "The games for some of your picks have started. These picks are not updated."
        end
      end
      flash[:notice] = "Your picks have been updated."
      redirect_to(:action=>"make")
    end
  end

  def review
    @title = "FFFF :: Review Picks"
    @tz = time_zone
    @games = Game.find_by_sql("SELECT games.id AS gid, games.gametime, games.week, games.home_score, games.away_score, games.is_bowl,
    picks.id AS pid, picks.home_score AS phscore, picks.away_score AS pascore,
    away.name AS aname, away.image AS aimg, away.location AS aloc, away.conference AS aconf, away.rankAP AS arankap, away.rankUSA AS arankusa, away.record AS arec, away.id AS aid, away.espnid AS aespnid, 
    home.name AS hname, home.image AS himg, home.location AS hloc, home.conference AS hconf, home.rankAP AS hrankap, home.rankUSA AS hrankusa, home.record AS hrec, home.id AS hid, home.espnid AS hespnid, 
    bowl.name AS bname, bowl.location AS bloc, bowl.multiplier AS bmult, bowl.url AS burl
    FROM games
    LEFT JOIN picks ON picks.game_id = games.id and picks.user_id = #{session[:user][:uid]}
    LEFT JOIN teams AS away ON away.id = games.away_team_id
    LEFT JOIN teams AS home ON home.id = games.home_team_id
    LEFT JOIN bowls AS bowl ON bowl.game_id = games.id
    ORDER BY games.week,games.gametime")
    @weeks = Game.find_by_sql("SELECT DISTINCT week FROM games ORDER BY week")
  end
end
