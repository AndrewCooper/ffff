class Score < ActiveRecord::Base
  belongs_to :user

  def self.user_stats (uid)
    stats = { :rank=>nil, :score=>nil, :ties=>[] }
    scores = Score.group(:user_id).sum(:total)
    if scores[uid]
      rank = 1
      scores.each do |user,score|
        if score > scores[uid]
          rank = rank + 1
        elsif score == scores[uid]
          if user != uid
            stats[:ties] << user
          end
        end
      end
      stats[:rank] = rank
      stats[:score] = scores[uid]
    end
    return stats
  end
end
