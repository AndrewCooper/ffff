require 'test_helper'

class LoginResponseCalculationTest < ActiveSupport::TestCase
  include LoginResponseCalculation

  TEST_PASS = "password"
  TEST_CHALLENGE = "challenge"
  TEST_RESPONSE = "b4de04a6f4e74b76bd84efe0d8151cb977ee9711"

  test 'should calculate response' do
    resp = calculate_login_response( TEST_PASS, TEST_CHALLENGE )
    assert_equal TEST_RESPONSE, resp
  end
end
