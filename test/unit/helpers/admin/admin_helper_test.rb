require 'test_helper'

class Admin::AdminHelperTest < ActionView::TestCase
  def setup
    super
    ENV['RAILS_ASSET_ID'] = ""
  end

  test 'edit_image' do
    tag = %(<img alt="asdf" src="/images/edit_off.png" onmouseover="this.src='/images/edit_on.png'" onmouseout="this.src='/images/edit_off.png'" />)
    assert_dom_equal( tag, edit_image( 'asdf' ) )
  end

  test 'delete_image' do
    tag = %(<img alt="asdf" src="/images/delete_off.png" onmouseover="this.src='/images/delete_on.png'" onmouseout="this.src='/images/delete_off.png'" />)
    assert_dom_equal( tag, delete_image( 'asdf' ) )
  end
end
