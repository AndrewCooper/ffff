class Admin::ScoreController < Admin::AdminController
def index
	@title = "Administration :: Scores"
end

def calculate
	@title = "Administration :: Calculation Results"
	if request.post?
		games = Game.find :all, :order=>"week,gametime"
		while games.size > 0
			wins = []
			wincounter = []
			perfects = []
			closests = []
			wmult = 1
			week = games[0].week
			weeks_games = games.select { |g| g.week == week }
			games = games.delete_if { |g| g.week == week }
			weeks_games.each do |game|
				closest = 2 ** (16)
				if bowl = game.bowl
					mult = bowl.multiplier
					wmult = 2
				else 
					mult = 1
				end
				if (game.away_score && game.home_score)
					picks = Pick.find :all, :conditions => "game_id = #{game.id}"
					picks.each do |pick|
						if !wins[pick.user_id]
							wins[pick.user_id] = wincounter[pick.user_id] = perfects[pick.user_id] = closests[pick.user_id] = 0
						end
						ph = pick.home_score
						pa = pick.away_score
						gh = game.home_score
						ga = game.away_score
						if (ph>pa&&gh>ga || ph<pa&&gh<ga || ph==pa&&gh==ga)
							wincounter[pick.user_id] += 1;
							wins[pick.user_id] += 1*mult
							score = pick.calculate_score(gh,ga)
							if score == 0 then perfects[pick.user_id] += 1*mult end
							if score < closest then closest = score end
						else
							score = -1
						end
						pick.pick_score = score
					end #picks.each
					picks.each do |pick|
						if pick.pick_score == closest
							pick.is_closest = 1
							closests[pick.user_id] += 1*mult
						else
							pick.is_closest = 0
						end
						pick.save
					end #picks.each
				end #if 
			end #games.each
	
			wins.each_index do |uid|
				if !wins[uid] then next end
				score = Score.find(:first,:conditions=>["week = ? and user_id = ?",week,uid]) || Score.new(:week=>week,:user_id=>uid)
				score.closests = closests[uid]
				score.perfects = perfects[uid]
				score.sevens = wincounter[uid] / weeks_games.size
				score.wins = wins[uid]
				score.total = score.wins + score.closests*2 + score.perfects*7 + score.sevens*7*wmult
				score.save
			end #wins.each
			week += 1
		end #while
		flash.now[:notice] = "Scores Calculated. Results follow."
	end
	@scores = Score.find(:all,:order=>"user_id,week",:include=>:user)
	update_session()
end #def calculate
end
