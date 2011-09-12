class ScoreController < ApplicationController
  before_filter :authorize_user, :except=>:rankings

  def rankings
    @title = "FFFF :: Home"
    update_session()
    @scores = User.ranked.all
  end

  def index
    @title = "FFFF :: Weekly Scores"

    @weeks = ActiveSupport::OrderedHash.new
    @users = {}
    User.select( [:id,:firstname,:lastname] ).all.each do |user|
      @users[user.id] = user
    end

    games = Game.order("week,gametime").includes( :away_team, :home_team, :bowl )
    games.each do |game|
      if @weeks[game.week].nil?
        @weeks[game.week] = {}
        @weeks[game.week][:games] = ActiveSupport::OrderedHash.new
        @weeks[game.week][:users] = ActiveSupport::OrderedHash.new
      end
      @weeks[game.week][:games][game.id] = game
    end

    Score.all.each do |score|
      if @weeks[score.week].nil?
        next
      end
      @weeks[score.week][:users][score.user_id] = {:score => score, :picks => {} }
    end

    Pick.select("picks.*,games.week").joins(:game).all.each do |pick|
      if @weeks[pick.week].nil?
        next
      end
      if @weeks[pick.week][:users][pick.user_id].nil?
        next
      end
      @weeks[pick.week][:users][pick.user_id][:picks][pick.game_id] = pick
    end
  end
end
