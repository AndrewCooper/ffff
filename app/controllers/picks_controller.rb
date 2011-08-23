class PicksController < ApplicationController
  # GET /picks
  def index
    @title = "FFFF :: Review Picks"
    @tz = time_zone

    @user=User.find(session[:user][:uid])

    matches = {}
    games = Game.order("week,gametime").includes([:away_team,:home_team,:bowl])
    games.each { |g| matches[g.id] = {:game=>g,:picks=>[]} }

    picks = Pick.where( :user_id=>@user.id )
    picks.each { |p| matches[p.game_id][:picks].push(p) }

    @weeks = {}
    matches.each do |gid,match|
      week = match[:game].week
      if @weeks[week].nil? then @weeks[week] = [] end
      @weeks[week] << match
    end
  end

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
end
