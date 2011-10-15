class PicksController < ApplicationController
  # GET /picks
  def index
    @title = "FFFF :: Review Picks"

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
    @user = User.find(session[:user][:uid])

    now = Time.now

    @weeks = {}
    games = Game.where("gametime > ?",now).order("week,gametime").includes([:away_team,:home_team,:bowl])
    Pick.transaction do
      games.each do |g| 
        week = g.week
        if @weeks[week].nil?
          @weeks[week] = []
        end
        match = {:game=>g,:picks=>[]}
        match[:picks] << Pick.find_or_create_by_game_id_and_user_id( :game_id=>g.id, :user_id=>@user.id )
        @weeks[week] << match
      end
    end
  end

  # PUT /picks
  def update
    @now = Time.now
    updated = 0
    overdue = 0
    # update existing picks
    Pick.transaction do
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
    end
    if updated > 0
      flash[:notice] = "#{updated} picks updated."
    end
    if overdue > 0
      flash[:warning] = "#{overdue} games have already started. These picks were not updated."
    end
    redirect_to(:action=>"index")
  end
end
