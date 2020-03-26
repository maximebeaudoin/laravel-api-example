Feature: Home

  Scenario: The api root endpoint must work properly
    When I request "GET /"
    Then I get a "200" response
    And the properties exist:
    """
    message
    """
    And the "message" property equals "Welcome to Laravel API example"
