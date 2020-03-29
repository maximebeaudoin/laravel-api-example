Feature: Users endpoint basic CRUD operations

  Scenario: Guest user wasn't authorized to call the api
    When I am a guest user
    When I request "GET /users"
    Then I get a "401" response

  Scenario: I can list all users
    When I request "GET /users"
    Then I get a "200" response
    And scope into the first "data" property
    And the "id" property equals "1"

  Scenario: I can show one user
    When I request "GET /users/1"
    Then I get a "200" response
    And scope into the "data" property
    And the "id" property equals "1"

  Scenario: I can create a new user
    Given I have the payload:
    """
    {"name":"John Doe","email":"john.doe@hotmail.com","password":"secretForTesting","job_title":"Software Engineer"}
    """
    When I request "POST /users"
    Then I get a "201" response
    And scope into the "data" property
    And the "name" property equals "John Doe"
    And the "email" property equals "john.doe@hotmail.com"
    And the "job_title" property equals "Software Engineer"

  Scenario: I try to create a new user but validation rules failed
    Given I have the payload:
    """
    {"name":"john"}
    """
    When I request "POST /users"
    Then I get a "422" response

  Scenario: I can update a existing user
    Given I have the payload:
    """
    {"name":"Jane Doe","email":"jane.doe@hotmail.com","job_title":"Operational Director"}
    """
    When I request "PATCH /users/1"
    Then I get a "200" response
    And scope into the "data" property
    And the "name" property equals "Jane Doe"
    And the "job_title" property equals "Operational Director"

  Scenario: I can update a existing user, no error should occur if i pass the same email
    Given I have the payload:
    """
    {"email":"jane.doe@hotmail.com"}
    """
    When I request "PATCH /users/1"
    Then I get a "200" response
    And scope into the "data" property
    And the "email" property equals "jane.doe@hotmail.com"

  Scenario: I can delete a existing user
    When I request "DELETE /users/2"
    Then I get a "204" response
