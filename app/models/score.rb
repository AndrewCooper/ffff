class Score < ActiveRecord::Base
  belongs_to :user
	
  def Score.user_stats (uid)
    myscore = Score.find_by_sql("SELECT user_id,SUM(total) AS sum_total FROM scores WHERE user_id=#{uid} GROUP BY user_id").first
    if myscore
      ties = []
      rank = nil
      allscores = Score.find_by_sql("SELECT user_id, SUM(total) AS sum_total, users.firstname, users.lastname FROM scores LEFT JOIN users ON users.id = user_id GROUP BY user_id ORDER BY sum_total DESC")
      allscores.each_index do |idx|
        if allscores[idx].sum_total == myscore.sum_total
          if allscores[idx].user_id != uid
            ties.push "#{allscores[idx].firstname} #{allscores[idx].lastname}"
          end
          if !rank then rank = idx+1 end
        end
      end
      {:rank=>rank,:score=>myscore.sum_total,:ties=>ties}
    else
      {:rank=>"N/A",:score=>"N/A",:ties=>[]}
    end
  end
end
