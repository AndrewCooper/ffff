# Methods added to this helper will be available to all templates in the application.
module ApplicationHelper
	def image_rollover (id, default, over, options = {})
		image_tag(default,{:id=>id,:onmouseover=>"change_image(\'#{id}\',\'#{image_path over}\')", :onmouseout=>"change_image(\'#{id}\',\'#{image_path default}\')"}.merge(options))
	end
end
