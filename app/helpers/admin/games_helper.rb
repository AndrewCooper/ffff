module Admin::GamesHelper

  def edit_image( alt_txt )
    image_tag( "edit_off.png", :alt=>alt_txt, :mouseover=>image_path("edit_on.png") )
  end

  def delete_image( alt_txt )
    image_tag( "delete_off.png", :alt=>alt_txt, :mouseover=>image_path("delete_on.png") )
  end
end
