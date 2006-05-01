class Admin::PickController < Admin::AdminController

  def index
		@title = "Administration :: Picks"
		@users = User.find :all,:order=>"lastname,firstname"
  end

	def edit
		if request.get?
			@user = User.find(@params[:id])
			if @user
				@title = "Administration :: Picks :: #{@user.firstname} #{@user.lastname}"
				@games = Game.find_by_sql(["SELECT games.id AS gid, games.gametime, games.week,
				picks.id AS pid, picks.home_score AS phscore, picks.away_score AS pascore, 
				away.name AS aname, away.image AS aimg, away.location AS aloc,  
				home.name AS hname, home.image AS himg, home.location AS hloc
				FROM games
				LEFT JOIN picks ON picks.game_id = games.id and picks.user_id = ?
				LEFT JOIN teams AS away ON away.id = games.away_team_id
				LEFT JOIN teams AS home ON home.id = games.home_team_id
				ORDER BY games.week DESC,games.gametime",@user.id])
			end
		else
			picks = @params["picks"]
			picks.each do |key,pick|
				game = Game.find(pick["game_id"])
				if pick["pick_id"] == ""
					dbpick = Pick.new
					dbpick.away_score = pick["away_score"] == "" ? 0 : pick["away_score"]
					dbpick.home_score = pick["home_score"] == "" ? 0 : pick["home_score"]
					dbpick.game_id = pick["game_id"]
					dbpick.user_id = pick["user_id"]
					dbpick.save
				else
					dbpick = Pick.find(pick["pick_id"].to_i)
					dbpick.home_score = pick["home_score"].to_i
					dbpick.away_score = pick["away_score"].to_i
					dbpick.save
				end
			end
			redirect_to(:action=>"edit")
		end
	end
end
