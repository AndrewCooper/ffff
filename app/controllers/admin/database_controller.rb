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
  
  def backup
    logger.info FFFF_BACKUP_DIR
    dbconfig = YAML.load_file("#{RAILS_ROOT}/config/database.yml")
    curcfg = dbconfig[ENV['RAILS_ENV']]
      
    if curcfg["adapter"]=="sqlite" || curcfg["adapter"]=="sqlite3" then
      dbfile = File.expand_path(curcfg["dbfile"], RAILS_ROOT)
      bakfile = File.expand_path(RAILS_ENV+".bak."+DateTime.now.to_s+".sqlite",FFFF_BACKUP_DIR)
      FileUtils.cp dbfile, bakfile
      if File.exists?bakfile then
        flash[:notice] = dbfile+" successfully backed up to "+bakfile
      else
        flash[:warning] = "An error occurred while backup up SQLite."
      end
    end
      
    if curcfg["adapter"]=="mysql" then
      bakfile = File.expand_path(RAILS_ENV+".bak."+DateTime.now.to_s+".mysql",FFFF_BACKUP_DIR)
      options = "-h#{curcfg["host"]} -u#{curcfg["username"]} -p#{curcfg["password"]} #{curcfg["database"]} > #{bakfile}"
      if system("mysqldump",options) then
        flash[:notice] = "Mysql successfully backed up to "+bakfile
      else
        flash[:warning] = "An error occurred while backup up Mysql."
      end
    end
    
    redirect_to :action=>:index
  end
end
