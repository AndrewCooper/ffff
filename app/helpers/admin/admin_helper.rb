module Admin::AdminHelper
  def edit_image( alt_txt )
    image_tag( "edit_off.png", :alt=>alt_txt, :mouseover=>image_path("edit_on.png") )
  end

  def delete_image( alt_txt )
    image_tag( "delete_off.png", :alt=>alt_txt, :mouseover=>image_path("delete_on.png") )
  end

  def team_logo( team )
    image_tag "/logos/#{team.image}",{:id=>"image#{team.id}", :alt=>"#{team.location} #{team.name} Logo"}
  end
end
