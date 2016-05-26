<?php
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;

class RevizContext implements Context, SnippetAcceptingContext
{


    /**
     * @Given the student wont to see a (:arg1)
     */
    public function theStudentWontToSeeA($arg1)
    {
        throw new PendingException();

    }

    /**
     * @Then then owner of resource they are a access to be true
     */
    public function thenOwnerOfResourceTheyAreAAccessToBeTrue()
    {
        throw new PendingException();
    }

}