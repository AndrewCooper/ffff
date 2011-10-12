require 'net/http'
require 'rexml/document'

class Admin::TeamsController < Admin::AdminController
  # GET /admin/teams
  def index
    @title = "Administration :: Teams"
    @item = Team.new
    @items = Team.order("location")
  end

  # POST /admin/teams
  def create
    @item = Team.create(params[:item])
    @items = Team.order("location")
    respond_to do |format|
      format.html { redirect_to admin_teams_path }
      format.js
    end
  end

  # GET /admin/teams/new
  def new
    @item = Team.new
    render :action=>:edit
  end

  # GET /admin/teams/:id/edit
  def edit
    @item = Team.find(params[:id])
  end

  # GET /admin/teams/:id
  def show
    @item = Team.find(params[:id])
  end

  # PUT /admin/teams/:id
  def update
    @item = Team.find(params[:id])
    if @item.update_attributes(params[:item])
      respond_to do |format|
        format.html { redirect_to admin_teams_path }
        format.js { render :action=>:show }
      end
    else
      respond_to do |format|
        format.html { redirect_to edit_admin_game_path(params[:id]) }
        format.js { render :action=>:edit }
      end
    end
  end

  # DELETE /admin/teams/:id
  def destroy
    @item = Team.find(params[:id])
    @item.destroy
    respond_to do |format|
      format.html { redirect_to admin_teams_path }
      format.js
    end
  end

  private
#  def update_from_espn
#    if params[:id]
#      @teams = [Team.find(params[:id])]
#    else
#      @teams = Team.find :all
#    end
#    host = "sports.espn.go.com"
#    Net::HTTP.start(host,80) do |http|
#      @teams.each do |team|
#        if team.espnid != 0
#          grab_info(http,team)
#          team.save
#        end
#      end
#    end
#    if params["logo"] == "true"
#      logos_dir = File.expand_path(FFFF_LOGOS_DIR)
#      if File.exists?(logos_dir) && File.directory?(logos_dir) && File.writable?(logos_dir) then
#        host = "espn-att.starwave.com"
#        Net::HTTP.start(host,80) do |http|
#          @teams.each do |team|
#            if team.espnid != 0
#              grab_logo(http,team)
#              team.save
#            end
#          end
#        end
#      else
#        logger.info "Error: #{logos_dir} does not exist, is not a directory, or is not writable."
#        flash[:warning] = "#{logos_dir} does not exist, is not a directory, or is not writable. "
#      end
#    end
#    if flash[:warning].nil? then
#      flash.now[:notice] = "Teams successfully updated"
#    end
#    redirect_to :action=>"index"
#  end
#
#  #http://espn.starwave.com/i/teamlogos/ncaa/med/#.gif
#  # def image
#  #   if params[:logofile]
#  #     File.open("#{RAILS_ROOT}/public/logos/#{params['filename']}", "wb") do |f|
#  #       f.write(params['logofile'].read)
#  #     end
#  #   end
#  # end
#
#  def grab_info(http,team)  #http://sports.espn.go.com/ncf/xml/teamInfoXML_2_0_0?teamId=###
#    path = "/ncf/xml/teamInfoXML_2_0_0?teamId=#{team.espnid}"
#    response = http.get(path)
#    if response.code == '200'
#      doc = REXML::Document.new response.body
#      team.location = doc.root.elements["location"].text.gsub("#a#","&")
#      team.record = doc.root.elements["record"].text
#      team.name = doc.root.elements["name"].text
#      team.conference = doc.root.elements["conferenceName"].text
#      team.color = doc.root.elements["color"].text
#      team.rankUSA = doc.root.elements["rankUSA"].text.to_i
#      team.rankAP = doc.root.elements["rankAP"].text.to_i
#    end
#  end
#
#  def grab_logo(http,team) #http://sports.espn.go.com/i/teamlogos/ncaa/med/#{team.espnid}.gif
#    img = "/i/teamlogos/ncaa/med/#{team.espnid}.gif"
#    response = http.get(img)
#    if response.code == '200'
#      n = "#{team.location} #{team.name}".gsub(" ","_")+".gif"
#      p = File.expand_path(n,FFFF_LOGOS_DIR)
#      logger.info "Attempting to write file #{p}"
#      File.open(p, "wb") do |f|
#        f.write(response.body)
#      end
#      team.image = n
#    end
#  end
end
