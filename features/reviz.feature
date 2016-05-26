@core

Feature: Reviz

  Student

  Scenario Outline: student have resources and Professor
    Given the student wont to see a <resourceCategoryIdPostId>
    Then then owner of resource they are a access to be <statusAccess>

    Examples:
      | resourceCategoryIdPostId | statusAccess |
      | (11,5)                   | true         |
      | (11,5)                   | true         |
      | (11,5)                   | true         |
      | (11,5)                   | true         |
