module ApplicationHelper
  def set_content_for(name, content = nil, &block)
    @_content_for[name] = ""
    content_for(name, content, &block)
  end
end

