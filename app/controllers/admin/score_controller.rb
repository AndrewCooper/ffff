class Admin::ScoreController < Admin::AdminController
  include ScoreCalculation

  def index
    @title = "Administration :: Scores"
  end

  def calculate
    @title = "Administration :: Calculation Results"
    calculate_scores
    update_session
    flash.now[:notice] = "Scores Calculated. Results follow."
    @scores = Score.find(:all,:order=>"user_id,week",:include=>:user)
  end
end
