class TeamController < ApplicationController
	before_filter :authorize_user, :except => [:index,:show]
  def index
		@title = "FFFF :: Browse Teams"
		@teams = Team.find(:all,:order=>"location")
  end

  def show
		@title = "FFFF :: Show Team"
		@team = Team.find(params[:id])
  end
end
