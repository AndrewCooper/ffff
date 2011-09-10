module ApplicationHelper
  def set_content_for(name, content = nil, &block)
    @_content_for[name] = ""
    content_for(name, content, &block)
  end

  def team_logo( team )
    image_tag "/logos/#{team.image}",{:id=>"image#{team.id}", :alt=>"#{team.location} #{team.name} Logo"}
  end
end

