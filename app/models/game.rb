class Game < ActiveRecord::Base
  has_one :bowl
  has_many :picks, :dependent=>:delete_all

  def self.find_with_teamnames(options = {})
    select = "SELECT games.*, away.name AS away_name, away.location AS away_loc, home.name AS home_name, home.location AS home_loc"
    from = "FROM games LEFT OUTER JOIN teams AS away ON away.id = games.away_team_id LEFT OUTER JOIN teams AS home ON home.id = games.home_team_id"
    where = options[:conditions] ? "WHERE #{options[:conditions]}" : ""
    #   where = options[:id] ? "WHERE games.id = #{options[:id]}" : ""
    order = options[:order] ? "ORDER BY #{options[:order]}" : ""
    limit = options[:limit] ? "LIMIT #{options[:limit]}" : ""
    offset = options[:offset] ? "OFFSET #{options[:offset]}" : ""
    Game.find_by_sql(select+" "+from+" "+where+" "+order+" "+limit+" "+offset)
  end

  def self.upcoming_games_with_picks(user_id = nil,time = nil)
    user_clause = if user_id.nil? then "" else "and picks.user_id = #{user_id}" end
    where_clause = if time.nil? then "" else "WHERE games.gametime >= '#{time.to_formatted_s(:db)}'" end
    @games = Game.find_by_sql("SELECT games.id AS gid, games.gametime, games.week, games.is_bowl,
    picks.id AS pid, picks.home_score AS phscore, picks.away_score AS pascore,
    away.name AS aname, away.image AS aimg, away.location AS aloc, away.conference AS aconf, away.rankAP AS arankap, away.rankUSA AS arankusa, away.record AS arec, away.id AS aid, away.espnid AS aespnid,
    home.name AS hname, home.image AS himg, home.location AS hloc, home.conference AS hconf, home.rankAP AS hrankap, home.rankUSA AS hrankusa, home.record AS hrec, home.id AS hid, home.espnid AS hespnid,
    bowl.name AS bname, bowl.location AS bloc, bowl.multiplier AS bmult, bowl.url AS burl
    FROM games
    LEFT JOIN picks ON picks.game_id = games.id #{user_clause}
    LEFT JOIN teams AS away ON away.id = games.away_team_id
    LEFT JOIN teams AS home ON home.id = games.home_team_id
    LEFT JOIN bowls AS bowl ON bowl.game_id = games.id
    #{where_clause}
    ORDER BY games.week,games.gametime")
  end
end
