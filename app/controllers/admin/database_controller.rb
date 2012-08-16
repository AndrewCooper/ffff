class Admin::DatabaseController < ApplicationController

  def index
    @title = "Administration :: Database"
  end
  
  def reset
    if request.post?
      tables = []
      if params["scores"]=="1" then
        Score.delete_all
        tables << "Scores"
      end
      if params["picks"]=="1" then
        Pick.delete_all
        tables << "Picks"
      end
      if params["bowls"]=="1" then
        Bowl.delete_all
        tables << "Bowls"
      end
      if params["games"]=="1" then
        Game.delete_all
        tables << "Games"
      end
      if params["teams"]=="1" then
        Team.delete_all
        tables << "Teams"
      end
      if params["users"]=="1" then
        User.delete_all
        tables << "Users"
      end
      flash[:notice] = "Tables "+tables.to_sentence+" have been reset."
    end
    redirect_to :action=>:index
  end
end
