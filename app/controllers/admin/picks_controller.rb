class Admin::PicksController < Admin::AdminController
  # GET /admin/picks
  def index
    @title = "Administration :: Picks"
    @users = User.order("lastname,firstname")
    @games = Game.order("week,gametime").includes([:away_team,:home_team])
  end

  # GET /admin/picks
  def edit
    if params[:user_id]
      @user=User.find(params[:user_id])

      matches = {}
      games = Game.order("week,gametime").includes([:away_team,:home_team])
      games.each { |g| matches[g.id] = {:game=>g,:picks=>[]} }

      picks = Pick.where( :user_id=>@user.id )
      picks.each { |p| matches[p.game_id][:picks].push(p) }

      @weeks = {}
      matches.each do |gid,match|
        week = match[:game].week 
        if @weeks[week].nil? then @weeks[week] = [] end
        @weeks[week] << match
      end
      render "edit_picks_user"
    elsif params[:game_id]
    elsif params[:week_id]
    end
  end
end
