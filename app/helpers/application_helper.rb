module ApplicationHelper
  def set_content_for(name, content = nil, &block)
    @_content_for[name] = ""
    content_for(name, content, &block)
  end

  def team_logo( team, options={} )
    if options[:id].nil?
      options[:id] = "image#{team.id}"
    end

    if options[:alt].nil?
      options[:alt] = "#{team.location} #{team.name} Logo"
    end

    image_tag "/logos/#{team.image}", options
  end
end

