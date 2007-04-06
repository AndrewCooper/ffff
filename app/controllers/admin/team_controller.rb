require 'net/http'
require 'rexml/document'

class Admin::TeamController < Admin::AdminController
	def delete
		Team.delete(@params[:id])
		render :nothing=>true
	end

	def edit
		@colspan = 7
		if @params[:id]
			@item = Team.find(@params[:id])
		else
			@item = Team.new
		end
		render :partial => "edit"
	end

	#http://espn.starwave.com/i/teamlogos/ncaa/med/#.gif
#	def image
#		if @params[:logofile]
#			File.open("#{RAILS_ROOT}/public/logos/#{@params['filename']}", "wb") do |f| 
#				f.write(@params['logofile'].read)
#			end
#		end
#	end

	def index
		@title = "Administration :: Teams"
	end
	
	def list
		if @params[:id]
			@item = Team.find(@params[:id])
			render(:partial => "item") and return
		else
			@items = Team.find(:all,:order=>"location")
		end
	end
	
	def new
		@item = Team.new
		render :partial=>"admin/shared/newitem"
	end

	def update
		if @params[:id]
			@item = Team.find(@params[:id])
			if @item.update_attributes(@params[:item])
				redirect_to :action => "list",:id=>@params[:id]
			else
			 	render_text @item.errors.inspect  + "\n"
			end
		else
			@item = Team.new(@params[:item])
			if @item.save
				redirect_to :action => "list","component"=>true
			else 
				logger.info "Errors: "+@item.errors.inspect
				render_text @item.errors.inspect  + "\n"
			end
		end
	end

	def update_from_espn
		if @params[:id]
			@teams = [Team.find(@params[:id])]
		else
			@teams = Team.find :all
		end
		host = "sports.espn.go.com"
		Net::HTTP.start(host,80) do |http|
			@teams.each do |team|
				if team.espnid != 0
					grab_info(http,team)
					team.save
				end
			end
		end
		if @params["logo"] == "true"
		  if File.exists?(FFFF_LOGOS_DIR) && File.directory?(FFFF_LOGOS_DIR) && File.writable?(FFFF_LOGOS_DIR) then
  			host = "espn-att.starwave.com"
  			Net::HTTP.start(host,80) do |http|
  				@teams.each do |team|
  					if team.espnid != 0
  						grab_logo(http,team)
  						team.save
  					end
  				end
  			end
			else
			  logger.info "Error: #{FFFF_LOGOS_DIR} does not exist, is not a directory, or is not writable."
			  flash[:warning] = "#{FFFF_LOGOS_DIR} does not exist, is not a directory, or is not writable. "
		  end
		end
		if flash[:warning].nil? then
  		flash.now[:notice] = "Teams successfully updated"
		end
		redirect_to :action=>"index"
	end

	private
	def grab_info(http,team)	#http://sports.espn.go.com/ncf/xml/teamInfoXML_2_0_0?teamId=###
		path = "/ncf/xml/teamInfoXML_2_0_0?teamId=#{team.espnid}"
		response = http.get(path)
		if response.code == '200'
			doc = REXML::Document.new response.body
			team.location = doc.root.elements["location"].text.gsub("#a#","&")
			team.record = doc.root.elements["record"].text
			team.name = doc.root.elements["name"].text
			team.conference = doc.root.elements["conferenceName"].text
			team.color = doc.root.elements["color"].text
			team.rankUSA = doc.root.elements["rankUSA"].text.to_i
			team.rankAP = doc.root.elements["rankAP"].text.to_i
		end
	end
	
	def grab_logo(http,team) #http://sports.espn.go.com/i/teamlogos/ncaa/med/#{team.espnid}.gif
		img = "/i/teamlogos/ncaa/med/#{team.espnid}.gif"
		response = http.get(img)
		if response.code == '200'
			n = "#{team.location} #{team.name}".gsub(" ","_")+".gif"
		  p = File.expand_path(n,FFFF_LOGOS_DIR)
		  logger.info "Attempting to write file #{p}"
			File.open(p, "wb") do |f| 
				f.write(response.body)
			end
			team.image = n
		end
	end
end
