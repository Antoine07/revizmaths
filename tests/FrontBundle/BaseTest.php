<?php

namespace Tests\FrontBundle;

use Reviz\FrontBundle\Entity\User;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand;



abstract class BaseTest extends WebTestCase
{
    protected $em;
    private $application;
    protected $container;

    public function setUp()
    {
        self::bootKernel();
        $kernel = static::$kernel;
        $this->container = $kernel->getContainer();
        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->application = new Application($kernel);

        // create the database
        $command = new CreateDatabaseDoctrineCommand();
        $this->application->add($command);
        $input = new ArrayInput([
            'command' => 'doctrine:database:create'
        ]);
        $command->run($input, new NullOutput());
        $command = new CreateSchemaDoctrineCommand();
        $this->application->add($command);
        $input = new ArrayInput([
            'command' => 'doctrine:schema:update',
            '--env' => 'test',
        ]);
        $command->run($input, new NullOutput());
        $this->setDataTerm('terms');
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        self::bootKernel();
        $kernel = static::$kernel;
        $this->application = new Application($kernel);
        // drop the database
        $command = new DropDatabaseDoctrineCommand();
        $this->application->add($command);
        $input = new ArrayInput(array(
            'command' => 'doctrine:database:drop',
            '--force' => true
        ));
        $command->run($input, new NullOutput());
        $this->em->close();
    }
    /**
     * getYaml
     *
     * @param $fileName
     * @return mixed
     */
    protected function getYaml($fileName)
    {
        $yaml = new Parser();
        $data = $yaml->parse(
            file_get_contents(__DIR__ . '/../_data/' . $fileName . '.yml')
        );
        return $data;
    }
    /**
     * set data terms level module and category
     *
     * @param $fileName
     *
     */
    protected function setDataTerm($fileName)
    {
        $data = $this->getYaml($fileName);
        $tax = function ($term) use ($data) {
            foreach ($data[$term] as $t) {
                $className = 'Reviz\FrontBundle\Entity\\' . $term;
                $entity = new $className;
                $entity->setName($t['name']);
                $entity->setParentId($t['parentId']);
                $this->em->persist($entity);
            }
        };
        $tax('Category');
        $tax('Module');
        $tax('Level');
        $this->em->flush();
    }
    /**
     * setUserData
     *
     * @description helper setUser data test
     * @param $fileName
     */
    protected function setUserData($fileName)
    {
        $data = $this->getYaml($fileName);
        if (empty($data['User'])) throw new \RuntimeException(sprintf(
            'fileName %s, do not match with key %',
            $fileName,
            'User'
        ));
        foreach ($data['User'] as $d) {
            $entity = new User;
            $entity->setUsername($d['username']);
            $entity->setEmail($d['email']);
            $entity->setPassword($d['password']);
            $this->em->persist($entity);
        }
        $this->em->flush();
    }
    /**
     * setDataPost
     *
     * @param $resource
     * @param $term
     */
    protected function setDataPost($resource, $term, $nb)
    {
        $yaml = new Parser();
        $data = $yaml->parse(
            file_get_contents(__DIR__ . '/../_data/data.yml')
        );
        $generator = $this->generator();
        foreach ($generator((int)$nb) as $r) {
            $className = 'Reviz\FrontBundle\Entity\\' . $resource;
            $entity = new $className;
            $entity->setTitle('titre resource' . rand(1, 1000));
            $entity->setContent('blabla');
            $entity->addTaxonomy($term);
            $this->em->persist($entity);
        }
        $this->em->flush();
    }
    protected function add()
    {
        return function ($term, $titleTerm, $resource, $nb) {
            $repository = 'RevizFrontBundle:' . ucfirst($term);
            $category = $this->em
                ->getRepository($repository)
                ->findByName($titleTerm);
            $category = $category[0];
            $this->setDataPost(ucfirst($resource), $category, $nb);
        };
    }
    protected function generator()
    {
        return function ($max) {
            for ($i = 1; $i <= $max; $i++) {
                yield $i;
            }
        };
    }
}