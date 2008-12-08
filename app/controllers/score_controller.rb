class ScoreController < ApplicationController
  before_filter :authorize_user, :except=>:index
	
  def index
  	@title = "FFFF :: Home"
    update_session()
  	@scores = Score.find_by_sql("SELECT users.id,SUM(scores.wins) AS sum_wins,SUM(scores.closests) AS sum_closests, SUM(scores.sevens) AS sum_sevens, SUM(scores.perfects) AS sum_perfects, SUM(scores.total) AS sum_total, users.firstname AS firstname, users.lastname AS lastname FROM users LEFT JOIN scores ON users.id = scores.user_id GROUP BY users.id ORDER BY sum_total DESC,users.lastname,users.firstname")
  end

  def show
    @title = "FFFF :: Weekly Scores"
    @now = current_app_time
    @gamecount = Game.count
    @response_body = ""
		
    @games_left = Game.find_by_sql("SELECT games.*,
		home.espnid AS hespnid, home.image AS homeimage,home.name AS homename, home.location as homeloc, 
		away.espnid AS aespnid, away.image AS awayimage,away.name AS awayname, away.location as awayloc,
		bowl.multiplier AS bmult 
		FROM games 
		LEFT JOIN teams AS away ON away.id=away_team_id 
		LEFT JOIN teams AS home ON home.id=home_team_id 
		LEFT JOIN bowls AS bowl ON bowl.game_id=games.id 
		ORDER BY week,gametime")
  	while @games_left.size > 0
      logger.debug "games_left: #{@games_left.size}"
      @week = @games_left[0].week
      logger.debug "week: #{@week}"
      @games = @games_left.select { |g| g.week == @week }
      logger.debug "games: #{@games.size}"
      @games_left = @games_left.delete_if { |g| g.week == @week }
      @gameids = {}
      @games.each_index do |idx|
        @gameids[@games[idx].id] = idx
      end
		
      @users = User.find_by_sql("SELECT users.id,users.lastname,users.firstname,scores.wins,scores.closests,scores.perfects,scores.sevens,scores.total FROM users LEFT OUTER JOIN scores ON users.id=scores.user_id and scores.week=#{@week} ORDER BY lastname,firstname")
      userids = {}
      @users.each_index do |idx|
        userids[@users[idx].id] = idx
      end

      @picks = Pick.find_by_sql("SELECT picks.*,users.firstname,users.lastname FROM picks LEFT OUTER JOIN users ON users.id=user_id WHERE game_id IN (#{@gameids.keys.join(",")}) ORDER BY users.lastname,users.firstname,game_id")
      @pickmatrix = Array.new(@users.size) {Array.new(@games.size)}
      @picks.each do |pick|
        if userids[pick.user_id] && @gameids[pick.game_id]
          @pickmatrix[userids[pick.user_id]][@gameids[pick.game_id]] = pick
        end
      end
			
      @response_body += render_to_string(:partial => "report_table")
			
      @week += 1
    end #while
    @weeks = Game.find_by_sql("SELECT DISTINCT week FROM games ORDER BY week")
  end
end
