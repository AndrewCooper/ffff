require 'test_helper'

class Admin::AdminHelperTest < ActionView::TestCase
  def setup
    super
    ENV['RAILS_ASSET_ID'] = ""
  end

  test 'edit_image' do
    tag = %(<img alt="asdf" src="/images/edit_off.png" onmouseover="this.src=&#x27;/images/edit_on.png&#x27;" onmouseout="this.src=&#x27;/images/edit_off.png&#x27;" />)
    assert_dom_equal( tag, edit_image( 'asdf' ) )
  end

  test 'delete_image' do
    tag = %(<img alt="asdf" src="/images/delete_off.png" onmouseover="this.src=&#x27;/images/delete_on.png&#x27;" onmouseout="this.src=&#x27;/images/delete_off.png&#x27;" />)
    assert_dom_equal( tag, delete_image( 'asdf' ) )
  end
end
