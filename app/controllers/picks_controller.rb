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

  # GET /picks/edit
  def edit
    @title = "FFFF :: Make Picks"
    @tz = time_zone
    @user = User.find(session[:user][:uid])

    now = current_app_time

    matches = {}
    games = Game.where("gametime > ?",now).order("week,gametime").includes([:away_team,:home_team,:bowl])
    games.each { |g| matches[g.id] = {:game=>g,:picks=>[]} }

    picks = Pick.where("game_id IN (?) AND user_id = ?",matches.keys,@user.id)
    picks.each { |p| matches[p.game_id][:picks].push(p) }

    @weeks = {}
    matches.each do |gid,match|
      week = match[:game].week
      if @weeks[week].nil? then @weeks[week] = [] end
      @weeks[week] << match
    end
  end

  # PUT /picks
  def update
    @now = current_app_time
    created = 0
    updated = 0
    overdue = 0
    Pick.transaction do
      # update existing picks
      picks = params["picks"]
      unless picks.nil?
        picks.each do |pid,pick|
          game = Game.find(pick["game_id"])
          if @now < game.gametime
            if Pick.update(pid,pick)
              updated += 1
            end
          else
            overdue += 1
          end
        end
      end

      # create missing picks
      picks = params["newpicks"]
      unless picks.nil?
        params["newpicks"].each do |pick|
          game = Game.find(pick["game_id"])
          if @now < game.gametime
            if Pick.create(pick)
              created += 1
            end
          else
            overdue += 1
          end
        end
      end
    end
    if created > 0
      flash[:notice] = "#{created} new picks created."
    end
    if updated > 0
      flash[:notice] = (flash.notice || "") + "#{updated} existing picks updated."
    end
    if overdue > 0
      flash[:warning] += "#{overdue} games have already started. These picks were not updated."
    end
    redirect_to(:action=>"index")
  end
end
