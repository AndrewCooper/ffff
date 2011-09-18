require 'test_helper'

class ApplicationHelperTest < ActionView::TestCase
  def setup
    super
    @_content_for = Hash.new {|h,k| h[k] = "" }
  end

  # Works with Rails/ActionPack 3.0.9
  test 'set_content_for' do
    assert ! content_for?( :asdf )

    set_content_for( :asdf, "asdf" )
    assert content_for?( :asdf )
    assert_equal "asdf", content_for(:asdf)

    content_for( :asdf, "ghjk" )
    assert_equal "asdfghjk", content_for(:asdf)

    set_content_for( :asdf, "qwer" )
    assert_equal "qwer", content_for(:asdf)
  end

  test 'team_logo' do
    ENV['RAILS_ASSET_ID'] = ""
    config.perform_caching = false

    team = teams(:pokes)
    tag = %(<img alt="#{team.location} #{team.name} Logo" id="image#{team.id}" src="/logos/#{team.image}" />)
    assert_dom_equal( tag, team_logo( teams(:pokes) ) )

    tag = %(<img alt="asdf" id="image#{team.id}" src="/logos/#{team.image}" />)
    assert_dom_equal( tag, team_logo( teams(:pokes), :alt=>"asdf" ) )

    tag = %(<img alt="#{team.location} #{team.name} Logo" id="asdf" src="/logos/#{team.image}" />)
    assert_dom_equal( tag, team_logo( teams(:pokes), :id=>"asdf" ) )

    tag = %(<img alt="#{team.location} #{team.name} Logo" id="image#{team.id}" src="/logos/#{team.image}" height="1234" width="4321" />)
    assert_dom_equal( tag, team_logo( teams(:pokes), :size=>"4321x1234" ) )
  end
end
